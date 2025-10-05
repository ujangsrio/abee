<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerBooking;
use App\Models\Layanan;
use App\Models\Slot;

class BookingController extends Controller
{
    // Tampilkan form booking
    public function create()
    {
        // Ambil semua layanan yang tanggal-nya belum lewat
        $layanans = Layanan::where('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal')
            ->get();

        // Kelompokkan layanan berdasarkan tanggal
        $tanggalJam = $layanans->groupBy('tanggal');

        return view('customer.booking.create', compact('tanggalJam'));
    }

    // Simpan booking
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:layanans,id',
            'time' => 'required|date_format:H:i',
            'tipe_pembayaran' => 'required|in:dp,full',
            'bukti_transfer' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = Auth::guard('customer')->user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return back()->with('error', 'Data customer tidak ditemukan.');
        }

        $layanan = Layanan::findOrFail($request->service_id);

        if (!$layanan->tanggal) {
            return back()->with('error', 'Layanan belum memiliki tanggal tersedia.');
        }

        $date = $layanan->tanggal;

        // Periksa apakah slot sudah dibooking
        $exists = CustomerBooking::where('date', $date)
            ->where('time', $request->time)
            ->where('service_id', $request->service_id)
            ->whereNotIn('status', ['Dibatalkan'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Jam tersebut sudah dibooking. Silakan pilih waktu lain.');
        }

        // Ambil tipe layanan dari model
        $tipeLayanan = $layanan->tipe_layanan ?? ['studio', 'home_service'];

        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiPath = $request->file('bukti_transfer')->store('bukti', 'public');
        }

        // Tentukan status DP berdasarkan tipe pembayaran
        $dpStatus = ($request->tipe_pembayaran === 'full') ? 'Lunas' : 'Belum';
        $status = 'Menunggu';

        CustomerBooking::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'service_id' => $request->service_id,
            'date' => $date,
            'time' => $request->time,
            'tipe_layanan' => $tipeLayanan,
            'status' => $status,
            'bukti_transfer' => $buktiPath,
            'tipe_pembayaran' => $request->tipe_pembayaran,
            'status_dp' => $dpStatus,
        ]);

        $message = $request->tipe_pembayaran === 'full'
            ? 'Booking berhasil! Pembayaran lunas sudah diterima.'
            : 'Booking berhasil! Menunggu konfirmasi pembayaran DP.';

        return redirect()->route('customer.reservasiaktif')->with('success', $message);
    }

    // API: Ambil jam tersedia
    public function availableTimes(Request $request)
    {
        $serviceId = $request->service_id;
        $layanan = Layanan::find($serviceId);

        if (!$layanan || !$layanan->tanggal) {
            return response()->json([]);
        }

        $tanggal = $layanan->tanggal;

        // Ambil semua slot yang cocok
        $slots = Slot::where('layanan_id', $serviceId)
            ->where('tanggal', $tanggal)
            ->pluck('jam');

        // Ambil jam yang sudah dibooking di tanggal tersebut (kecuali yang dibatalkan)
        $bookedTimes = CustomerBooking::where('service_id', $serviceId)
            ->where('date', $tanggal)
            ->whereNotIn('status', ['Dibatalkan'])
            ->pluck('time')
            ->toArray();

        // Filter slot yang belum dibooking
        $availableSlots = $slots->filter(function ($jam) use ($bookedTimes) {
            return !in_array($jam, $bookedTimes);
        })->map(function ($jam) {
            return ['jam' => $jam];
        })->values();

        return response()->json($availableSlots);
    }

    // Hitung total biaya berdasarkan layanan
    public function calculateTotalCost(Request $request)
    {
        $serviceId = $request->service_id;
        $layanan = Layanan::with('promo')->find($serviceId);

        if (!$layanan) {
            return response()->json(['error' => 'Layanan tidak ditemukan'], 404);
        }

        $user = Auth::guard('customer')->user();
        $customer = Customer::where('user_id', $user->id)->first();
        $isMember = $customer && $customer->is_member;

        $hargaLayanan = $layanan->harga;
        $diskon = 0;
        $totalSetelahDiskon = $hargaLayanan;

        // Cek jika ada promo
        if ($layanan->promo) {
            $promo = $layanan->promo;
            // Cek apakah promo berlaku (belum expired)
            $isPromoValid = !$promo->tanggal_berakhir || now()->lte($promo->tanggal_berakhir);

            if ($isPromoValid) {
                // Cek apakah promo hanya untuk member atau untuk semua
                if (!$promo->hanya_member || ($promo->hanya_member && $isMember)) {
                    $diskon = ($promo->diskon / 100) * $hargaLayanan;
                    $totalSetelahDiskon = $hargaLayanan - $diskon;
                }
            }
        }

        $dp = 50000; // DP tetap Rp50.000
        $sisaPembayaran = max(0, $totalSetelahDiskon - $dp);

        // Ambil tipe layanan dari database
        $tipeLayanan = $layanan->tipe_layanan ?? ['studio', 'home_service'];

        return response()->json([
            'service_name' => $layanan->nama,
            'service_type' => $tipeLayanan,
            'base_price' => $hargaLayanan,
            'discount' => $diskon,
            'total_after_discount' => $totalSetelahDiskon,
            'dp' => $dp,
            'remaining_payment' => $sisaPembayaran,
            'is_member' => $isMember,
            'promo_name' => $layanan->promo ? $layanan->promo->nama_promo : null
        ]);
    }

    public function show($id)
    {
        $booking = CustomerBooking::with('service')->findOrFail($id);
        return view('customer.booking.detail', compact('booking'));
    }

    public function reservasiAktif()
    {
        $user = Auth::guard('customer')->user();
        $customerProfile = Customer::where('user_id', $user->id)->first();

        if (!$customerProfile) {
            return back()->withErrors(['msg' => 'Data pelanggan tidak ditemukan.']);
        }

        // Pastikan eager loading dengan relasi service
        $bookings = CustomerBooking::with(['service' => function ($query) {
            $query->select('id', 'nama', 'harga', 'promo_id'); // Select hanya field yang diperlukan
        }])
            ->where('customer_id', $customerProfile->id)
            ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        $isMember = $customerProfile->is_member ?? false;

       

        // Hitung biaya untuk setiap booking
        $bookingsWithCost = $bookings->map(function ($booking) use ($isMember) {
            $layanan = $booking->service;

            if (!$layanan) {
                $booking->cost_info = [
                    'base_price' => 0,
                    'discount' => 0,
                    'total_after_discount' => 0,
                    'dp' => 50000,
                    'remaining_payment' => 0,
                    'promo_name' => null
                ];
                return $booking;
            }

            $hargaLayanan = $layanan->harga;
            $diskon = 0;
            $totalSetelahDiskon = $hargaLayanan;
            $promoName = null;

            // Cek jika ada promo
            if ($layanan->promo_id) {
                $promo = \App\Models\Promo::find($layanan->promo_id);
                if ($promo) {
                    $isPromoValid = !$promo->tanggal_berakhir || now()->lte($promo->tanggal_berakhir);
                    if ($isPromoValid) {
                        if (!$promo->hanya_member || ($promo->hanya_member && $isMember)) {
                            $diskon = ($promo->diskon / 100) * $hargaLayanan;
                            $totalSetelahDiskon = $hargaLayanan - $diskon;
                            $promoName = $promo->nama_promo;
                        }
                    }
                }
            }

            $dp = 50000;
            $isDpConfirmed = $booking->status_dp === 'Lunas';
            $isFullPayment = $booking->tipe_pembayaran === 'full';

            if ($isFullPayment) {
                $sisaPembayaran = 0;
            } else {
                $sisaPembayaran = $isDpConfirmed ? max(0, $totalSetelahDiskon - $dp) : $totalSetelahDiskon - $dp;
            }

            $booking->cost_info = [
                'base_price' => $hargaLayanan,
                'discount' => $diskon,
                'total_after_discount' => $totalSetelahDiskon,
                'dp' => $dp,
                'remaining_payment' => max(0, $sisaPembayaran),
                'promo_name' => $promoName,
                'is_dp_confirmed' => $isDpConfirmed,
                'is_full_payment' => $isFullPayment
            ];

            return $booking;
        });

        return view('customer.reservasiaktif.index', [
            'bookings' => $bookingsWithCost,
            'isMember' => $isMember,
        ]);
    }

    public function cancel($id)
    {
        $booking = CustomerBooking::findOrFail($id);

        // Pastikan booking milik user yang login
        $user = Auth::guard('customer')->user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer || $booking->customer_id !== $customer->id) {
            abort(403, 'Tidak diizinkan.');
        }

        if ($booking->status !== 'Menunggu') {
            return back()->with('error', 'Reservasi hanya bisa dibatalkan jika masih menunggu konfirmasi.');
        }

        $booking->update([
            'status' => 'Dibatalkan',
            'status_dp' => 'Belum'
        ]);

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }

    public function history()
    {
        $user = Auth::guard('customer')->user();
        $customer = Customer::where('user_id', $user->id)->first();

        if (!$customer) {
            return back()->with('error', 'Data customer tidak ditemukan.');
        }

        $bookings = CustomerBooking::with('service')
            ->where('customer_id', $customer->id)
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get();

        return view('customer.history.index', compact('bookings'));
    }
}

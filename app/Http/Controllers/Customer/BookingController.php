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
            'payment_type' => 'required|in:dp,full',
            'bukti_transfer' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // validasi file
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

        $exists = CustomerBooking::where('date', $date)
            ->where('time', $request->time)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Jam tersebut sudah dibooking. Silakan pilih waktu lain.');
        }

        // ⬇️ Upload file jika ada
        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiPath = $request->file('bukti_transfer')->store('bukti', 'public');
        }

        // Tentukan dp_status berdasarkan jenis pembayaran
        $dpStatus = ($request->payment_type === 'full') ? 'Lunas' : 'Belum';

        CustomerBooking::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'service_id' => $request->service_id,
            'date' => $date,
            'time' => $request->time,
            'status' => 'Menunggu',
            'bukti_transfer' => $buktiPath, // simpan path bukti transfer
            'payment_type' => $request->payment_type,
            'dp_status' => $dpStatus,
        ]);

        $message = $request->payment_type === 'full' ?
            'Booking berhasil! Pembayaran lunas sudah diterima.' :
            'Booking berhasil! Menunggu konfirmasi pembayaran DP.';

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

        // Ambil jam yang sudah dibooking di tanggal tersebut
        $bookedTimes = CustomerBooking::where('service_id', $serviceId)
            ->where('date', $tanggal)
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

    // API: Hitung total biaya berdasarkan layanan
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

        return response()->json([
            'service_name' => $layanan->nama,
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

        $bookings = CustomerBooking::with(['service', 'service.promo'])
            ->where('customer_id', $customerProfile->id)
            ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
            ->orderBy('date', 'asc')
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
            if ($layanan->promo) {
                $promo = $layanan->promo;
                // Cek apakah promo berlaku (belum expired)
                $isPromoValid = !$promo->tanggal_berakhir || now()->lte($promo->tanggal_berakhir);

                if ($isPromoValid) {
                    // Cek apakah promo hanya untuk member atau untuk semua
                    if (!$promo->hanya_member || ($promo->hanya_member && $isMember)) {
                        $diskon = ($promo->diskon / 100) * $hargaLayanan;
                        $totalSetelahDiskon = $hargaLayanan - $diskon;
                        $promoName = $promo->nama_promo;
                    }
                }
            }

            $dp = 50000; // DP tetap Rp50.000

            // Sisa pembayaran hanya dihitung jika DP sudah dikonfirmasi atau pembayaran full
            $isDpConfirmed = $booking->dp_status === 'Lunas' || $booking->dp_status === 'Dikonfirmasi';
            $isFullPayment = $booking->payment_type === 'full';

            // Jika pembayaran full, sisa pembayaran = 0. Jika DP dan dikonfirmasi, hitung sisa
            if ($isFullPayment) {
                $sisaPembayaran = 0;
            } else {
                $sisaPembayaran = $isDpConfirmed ? max(0, $totalSetelahDiskon - $dp) : 0;
            }

            $booking->cost_info = [
                'base_price' => $hargaLayanan,
                'discount' => $diskon,
                'total_after_discount' => $totalSetelahDiskon,
                'dp' => $dp,
                'remaining_payment' => $sisaPembayaran,
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

        if ($booking->customer_name !== Auth::guard('customer')->user()->name) {
            abort(403, 'Tidak diizinkan.');
        }

        if ($booking->status !== 'Menunggu') {
            return back()->with('error', 'Reservasi hanya bisa dibatalkan jika masih menunggu konfirmasi.');
        }

        $booking->status = 'Dibatalkan';
        $booking->save();
        $booking->delete();

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
            ->whereIn('status', ['Menunggu', 'Dikonfirmasi', 'Selesai', 'Dibatalkan'])
            ->orderBy('date', 'desc')
            ->get();

        return view('customer.history.index', compact('bookings'));
    }
}

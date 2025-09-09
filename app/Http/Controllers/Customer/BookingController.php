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

        CustomerBooking::create([
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'service_id' => $request->service_id,
            'date' => $date,
            'time' => $request->time,
            'status' => 'Menunggu',
            'bukti_transfer' => $buktiPath, // simpan path bukti transfer
        ]);

        return redirect()->route('customer.reservasiaktif')->with('success', 'Booking berhasil!');
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

        $bookings = CustomerBooking::with('service')
            ->where('customer_id', $customerProfile->id)
            ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
            ->orderBy('date', 'asc')
            ->get();

        return view('customer.reservasiaktif.index', [
            'bookings' => $bookings,
            'isMember' => $customerProfile->is_member ?? false,
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

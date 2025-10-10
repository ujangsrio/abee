<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerBooking;
use App\Models\Layanan;
use App\Models\Slot;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function create()
    {
        try {
            // Ambil layanan yang aktif dengan slot tersedia
            $layananWithSlots = Layanan::whereHas('slots', function ($query) {
                $query->where('tanggal', '>=', now()->format('Y-m-d'));
            })
                ->with(['slots' => function ($query) {
                    $query->where('tanggal', '>=', now()->format('Y-m-d'))
                        ->orderBy('tanggal')
                        ->orderBy('jam');
                }])
                ->get();

            // Format data untuk dropdown
            $tanggalJam = [];
            foreach ($layananWithSlots as $layanan) {
                foreach ($layanan->slots as $slot) {
                    $tanggal = is_object($slot->tanggal) ? $slot->tanggal->format('Y-m-d') : $slot->tanggal;

                    if (!isset($tanggalJam[$tanggal])) {
                        $tanggalJam[$tanggal] = [];
                    }

                    $exists = false;
                    foreach ($tanggalJam[$tanggal] as $existingLayanan) {
                        if ($existingLayanan->id === $layanan->id) {
                            $exists = true;
                            break;
                        }
                    }

                    if (!$exists) {
                        $tanggalJam[$tanggal][] = $layanan;
                    }
                }
            }

            ksort($tanggalJam);

            return view('customer.booking.create', compact('tanggalJam'));
        } catch (\Exception $e) {
            Log::error('Error in BookingController@create: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form booking.');
        }
    }

    public function availableTimes(Request $request)
    {
        try {
            $request->validate([
                'service_id' => 'required|exists:layanans,id',
                'tanggal' => 'required|date'
            ]);

            $serviceId = $request->service_id;
            $tanggal = $request->tanggal;

            $availableSlots = Slot::where('layanan_id', $serviceId)
                ->where('tanggal', $tanggal)
                ->whereNotExists(function ($query) use ($serviceId, $tanggal) {
                    $query->select(DB::raw(1))
                        ->from('customer_bookings')
                        ->whereColumn('customer_bookings.service_id', 'slots.layanan_id')
                        ->where('customer_bookings.date', $tanggal)
                        ->whereColumn('customer_bookings.time', 'slots.jam')
                        ->whereIn('customer_bookings.status', ['Menunggu', 'Dikonfirmasi']);
                })
                ->get()
                ->map(function ($slot) {
                    return [
                        'jam' => \Carbon\Carbon::parse($slot->jam)->format('H:i')
                    ];
                });

            return response()->json($availableSlots);
        } catch (\Exception $e) {
            Log::error('Error in availableTimes: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    public function calculateTotalCost(Request $request)
    {
        try {
            $request->validate([
                'service_id' => 'required|exists:layanans,id'
            ]);

            $serviceId = $request->service_id;
            $layanan = Layanan::findOrFail($serviceId);

            $basePrice = $layanan->harga;
            $discount = 0;
            $promoName = null;
            $totalAfterDiscount = $basePrice - $discount;
            $dp = 50000;
            $remainingPayment = $totalAfterDiscount - $dp;

            $serviceType = $layanan->tipe_layanan;

            if (is_string($serviceType)) {
                try {
                    $decoded = json_decode($serviceType, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $serviceType = $decoded;
                    }
                } catch (\Exception $e) {
                    // Tetap gunakan string asli
                }
            }

            if (empty($serviceType)) {
                $serviceType = ['studio'];
            }

            if (!is_array($serviceType)) {
                $serviceType = [$serviceType];
            }

            return response()->json([
                'service_name' => $layanan->nama,
                'base_price' => $basePrice,
                'discount' => $discount,
                'promo_name' => $promoName,
                'total_after_discount' => $totalAfterDiscount,
                'dp' => $dp,
                'remaining_payment' => $remainingPayment,
                'service_type' => $serviceType
            ]);
        } catch (\Exception $e) {
            Log::error('Error in calculateTotalCost: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung biaya'], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:layanans,id',
            'time' => 'required',
            'tanggal' => 'required|date',
            'tipe_pembayaran' => 'required|in:dp,full',
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $customer = Auth::guard('customer')->user();
            $customerProfile = $customer->customer;

            if (!$customerProfile) {
                return back()->with('error', 'Profil customer tidak ditemukan.');
            }

            $buktiTransferPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $buktiTransferPath = $request->file('bukti_transfer')->store('bukti', 'public');
            }

            $tipeLayanan = $request->tipe_layanan ?: 'studio';
            $tipeLayananArray = is_array($tipeLayanan) ? $tipeLayanan : [$tipeLayanan];

            $booking = CustomerBooking::create([
                'customer_id' => $customerProfile->id,
                'customer_name' => $customer->name,
                'service_id' => $request->service_id,
                'date' => $request->tanggal,
                'time' => $request->time,
                'tipe_layanan' => json_encode($tipeLayananArray),
                'status' => 'Menunggu',
                'status_dp' => 'Belum',
                'tipe_pembayaran' => $request->tipe_pembayaran,
                'bukti_transfer' => $buktiTransferPath,
            ]);

            return redirect()->route('customer.booking.show', $booking->id)
                ->with('success', 'Booking berhasil dibuat! Silakan tunggu konfirmasi admin.');
        } catch (\Exception $e) {
            Log::error('Error in store booking: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reservasiaktif()
    {
        try {
            $customer = Auth::guard('customer')->user();
            $customerProfile = $customer->customer;

            if (!$customerProfile) {
                return redirect()->route('customer.dashboard')
                    ->with('error', 'Profil customer tidak ditemukan.');
            }

            $bookings = CustomerBooking::with(['service' => function ($query) {
                $query->select('id', 'nama', 'harga', 'deskripsi', 'gambar', 'tipe_layanan');
            }])
                ->where('customer_id', $customerProfile->id)
                ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->get();

            foreach ($bookings as $booking) {
                $booking->cost_info = $this->calculateBookingCost($booking);
            }

            $isMember = $customerProfile->is_member;

            // Arahkan ke view dalam subfolder reservasiaktif
            return view('customer.reservasiaktif.index', compact('bookings', 'isMember'));
        } catch (\Exception $e) {
            Log::error('Error in reservasiaktif: ' . $e->getMessage());
            return redirect()->route('customer.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat reservasi aktif.');
        }
    }


    public function history()
    {
        try {
            $customer = Auth::guard('customer')->user();
            $customerProfile = $customer->customer;

            if (!$customerProfile) {
                return redirect()->route('customer.dashboard')
                    ->with('error', 'Profil customer tidak ditemukan.');
            }

            $bookings = CustomerBooking::with(['service' => function ($query) {
                $query->select('id', 'nama', 'harga', 'deskripsi', 'gambar', 'tipe_layanan');
            }])
                ->where('customer_id', $customerProfile->id)
                ->whereIn('status', ['Selesai', 'Dibatalkan'])
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->get();

            foreach ($bookings as $booking) {
                $booking->cost_info = $this->calculateBookingCost($booking);
            }

            // Arahkan ke view dalam subfolder history
            return view('customer.history.index', compact('bookings'));
        } catch (\Exception $e) {
            Log::error('Error in history: ' . $e->getMessage());
            return redirect()->route('customer.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat history.');
        }
    }

    public function show($id)
    {
        try {
            $customer = Auth::guard('customer')->user();
            $customerProfile = $customer->customer;

            $booking = CustomerBooking::with(['service' => function ($query) {
                $query->select('id', 'nama', 'harga', 'deskripsi', 'gambar', 'tipe_layanan');
            }])
                ->where('id', $id)
                ->where('customer_id', $customerProfile->id)
                ->firstOrFail();

            $booking->cost_info = $this->calculateBookingCost($booking);

            // Jika show.blade.php ada dalam subfolder booking
            return view('customer.booking.show', compact('booking'));
        } catch (\Exception $e) {
            Log::error('Error in show booking: ' . $e->getMessage());
            return redirect()->route('customer.reservasiaktif')
                ->with('error', 'Booking tidak ditemukan.');
        }
    }

    public function cancel($id)
    {
        try {
            $customer = Auth::guard('customer')->user();
            $customerProfile = $customer->customer;

            $booking = CustomerBooking::where('id', $id)
                ->where('customer_id', $customerProfile->id)
                ->firstOrFail();

            if ($booking->status !== 'Menunggu') {
                return back()->with('error', 'Hanya booking dengan status Menunggu yang dapat dibatalkan.');
            }

            $booking->update(['status' => 'Dibatalkan']);

            return redirect()->route('customer.reservasiaktif')
                ->with('success', 'Booking berhasil dibatalkan.');
        } catch (\Exception $e) {
            Log::error('Error in cancel booking: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membatalkan booking.');
        }
    }

    public function availableTimesByDate(Request $request)
    {
        try {
            $request->validate([
                'service_id' => 'required|exists:layanans,id',
                'tanggal' => 'required|date'
            ]);

            $availableSlots = Slot::where('layanan_id', $request->service_id)
                ->where('tanggal', $request->tanggal)
                ->whereNotExists(function ($query) use ($request) {
                    $query->select(DB::raw(1))
                        ->from('customer_bookings')
                        ->whereColumn('customer_bookings.service_id', 'slots.layanan_id')
                        ->where('customer_bookings.date', $request->tanggal)
                        ->whereColumn('customer_bookings.time', 'slots.jam')
                        ->whereIn('customer_bookings.status', ['Menunggu', 'Dikonfirmasi']);
                })
                ->get()
                ->map(function ($slot) {
                    return [
                        'jam' => \Carbon\Carbon::parse($slot->jam)->format('H:i')
                    ];
                });

            return response()->json($availableSlots);
        } catch (\Exception $e) {
            Log::error('Error in availableTimesByDate: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Hitung informasi biaya untuk booking
     */
    private function calculateBookingCost($booking)
    {
        $basePrice = $booking->service->harga ?? 0;
        $discount = 0;
        $promoName = null;

        if ($booking->service && $booking->service->is_promo) {
            $discount = $basePrice * 0.1;
            $promoName = 'Promo Spesial';
        }

        $totalAfterDiscount = $basePrice - $discount;

        $isFullPayment = $booking->tipe_pembayaran === 'full';
        $isDpConfirmed = $booking->status_dp === 'Lunas';
        $dp = $isFullPayment ? 0 : 50000;
        $remainingPayment = $isFullPayment ? 0 : ($totalAfterDiscount - $dp);

        return [
            'base_price' => $basePrice,
            'discount' => $discount,
            'promo_name' => $promoName,
            'total_after_discount' => $totalAfterDiscount,
            'dp' => $dp,
            'remaining_payment' => $remainingPayment,
            'is_full_payment' => $isFullPayment,
            'is_dp_confirmed' => $isDpConfirmed,
        ];
    }
}

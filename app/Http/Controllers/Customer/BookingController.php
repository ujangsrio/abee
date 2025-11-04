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
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        try {
            $selectedService = null;
            $availableDates = [];

            // Jika ada service_id dari card layanan
            if ($request->has('service_id')) {
                $selectedService = Layanan::with('promo')->find($request->service_id);

                if ($selectedService) {
                    // Parse jadwal operasional
                    $waktuOperasional = $selectedService->waktu_operasional;
                    $hariOperasional = [];
                    $jamBukaDefault = '08:00';
                    $jamTutupDefault = '17:00';

                    if ($waktuOperasional && is_array($waktuOperasional)) {
                        $hariOperasional = $waktuOperasional['hari_operasional'] ?? [];
                        $jamBukaDefault = $waktuOperasional['jam_buka_default'] ?? '08:00';
                        $jamTutupDefault = $waktuOperasional['jam_tutup_default'] ?? '17:00';
                    } elseif (is_string($waktuOperasional)) {
                        try {
                            $decoded = json_decode($waktuOperasional, true);
                            if ($decoded) {
                                $hariOperasional = $decoded['hari_operasional'] ?? [];
                                $jamBukaDefault = $decoded['jam_buka_default'] ?? '08:00';
                                $jamTutupDefault = $decoded['jam_tutup_default'] ?? '17:00';
                            }
                        } catch (\Exception $e) {
                            Log::error('Error parsing waktu operasional: ' . $e->getMessage());
                        }
                    }

                    // Map hari Indonesia
                    $hariMap = [
                        'senin' => 'Sen',
                        'selasa' => 'Sel',
                        'rabu' => 'Rab',
                        'kamis' => 'Kam',
                        'jumat' => 'Jum',
                        'sabtu' => 'Sab',
                        'minggu' => 'Min'
                    ];

                    // Map hari Indonesia ke Inggris untuk Carbon
                    $dayMapping = [
                        'senin' => 'monday',
                        'selasa' => 'tuesday',
                        'rabu' => 'wednesday',
                        'kamis' => 'thursday',
                        'jumat' => 'friday',
                        'sabtu' => 'saturday',
                        'minggu' => 'sunday'
                    ];

                    // Generate available dates berdasarkan hari operasional
                    $startDate = Carbon::now();
                    $endDate = Carbon::now()->addDays(30); // 30 hari ke depan

                    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                        $dayName = strtolower($date->format('l'));
                        $dayNameIndo = array_search($dayName, $dayMapping);

                        if (in_array($dayNameIndo, $hariOperasional)) {
                            $hariSingkat = $hariMap[$dayNameIndo] ?? 'Sen';

                            $availableDates[$date->format('Y-m-d')] = [
                                'hari_singkat' => $hariSingkat,
                                'jam_buka' => $jamBukaDefault,
                                'jam_tutup' => $jamTutupDefault,
                                'tanggal_format' => $date->format('d M Y')
                            ];
                        }
                    }

                    // Jika tidak ada tanggal yang tersedia, beri pesan
                    if (empty($availableDates)) {
                        Log::warning('Tidak ada tanggal tersedia untuk layanan: ' . $selectedService->nama);
                    }
                }
            }

            if (!$selectedService) {
                return redirect()->route('customer.layanan')
                    ->with('error', 'Silakan pilih layanan terlebih dahulu.');
            }

            return view('customer.booking.create', compact('selectedService', 'availableDates'));
        } catch (\Exception $e) {
            Log::error('Error in BookingController@create: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat form booking: ' . $e->getMessage());
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

            // Ambil layanan untuk mendapatkan jadwal operasional
            $layanan = Layanan::find($serviceId);
            $waktuOperasional = $layanan->waktu_operasional;

            $jamBukaDefault = '08:00';
            $jamTutupDefault = '17:00';

            if ($waktuOperasional && is_array($waktuOperasional)) {
                $jamBukaDefault = $waktuOperasional['jam_buka_default'] ?? '08:00';
                $jamTutupDefault = $waktuOperasional['jam_tutup_default'] ?? '17:00';
            }

            // Generate time slots berdasarkan jam operasional
            $availableSlots = [];
            $startTime = Carbon::createFromTimeString($jamBukaDefault);
            $endTime = Carbon::createFromTimeString($jamTutupDefault);

            // Buat slot setiap 30 menit
            for ($time = $startTime; $time->lt($endTime); $time->addMinutes(30)) {
                $slotTime = $time->format('H:i');

                // Cek apakah slot tersedia (tidak ada booking yang bentrok)
                $isAvailable = !CustomerBooking::where('service_id', $serviceId)
                    ->where('date', $tanggal)
                    ->where('time', $slotTime)
                    ->whereIn('status', ['Menunggu', 'Dikonfirmasi'])
                    ->exists();

                if ($isAvailable) {
                    $availableSlots[] = [
                        'jam' => $slotTime,
                        'formatted' => $slotTime
                    ];
                }
            }

            return response()->json($availableSlots);
        } catch (\Exception $e) {
            Log::error('Error in availableTimes: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat jam tersedia'], 500);
        }
    }

    public function availableTimesByDate(Request $request)
    {
        try {
            $request->validate([
                'service_id' => 'required|exists:layanans,id',
                'tanggal' => 'required|date'
            ]);

            $serviceId = $request->service_id;
            $tanggal = $request->tanggal;

            // Ambil layanan untuk mendapatkan jadwal operasional
            $layanan = Layanan::find($serviceId);
            $waktuOperasional = $layanan->waktu_operasional;

            $jamBukaDefault = '08:00';
            $jamTutupDefault = '17:00';

            if ($waktuOperasional && is_array($waktuOperasional)) {
                $jamBukaDefault = $waktuOperasional['jam_buka_default'] ?? '08:00';
                $jamTutupDefault = $waktuOperasional['jam_tutup_default'] ?? '17:00';
            }

            // Generate time slots
            $availableSlots = [];
            $startTime = Carbon::createFromTimeString($jamBukaDefault);
            $endTime = Carbon::createFromTimeString($jamTutupDefault);

            for ($time = $startTime; $time->lt($endTime); $time->addMinutes(30)) {
                $slotTime = $time->format('H:i');

                $isAvailable = !CustomerBooking::where('service_id', $serviceId)
                    ->where('date', $tanggal)
                    ->where('time', $slotTime)
                    ->whereIn('status', ['Menunggu', 'Dikonfirmasi'])
                    ->exists();

                if ($isAvailable) {
                    $availableSlots[] = [
                        'jam' => $slotTime,
                        'formatted' => $slotTime
                    ];
                }
            }

            return response()->json($availableSlots);
        } catch (\Exception $e) {
            Log::error('Error in availableTimesByDate: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat jam tersedia'], 500);
        }
    }



    public function calculateTotalCost(Request $request)
    {
        try {
            $request->validate([
                'service_id' => 'required|exists:layanans,id'
            ]);

            $serviceId = $request->service_id;
            // Eager load 'promo' untuk mendapatkan data diskon yang akurat
            $layanan = Layanan::with('promo')->findOrFail($serviceId);

            $basePrice = $layanan->harga;
            $discount = 0;
            $promoName = null;

            // --- LOGIKA PERHITUNGAN DISKON BARU (Menggunakan Relasi Promo) ---
            if ($layanan->promo) {
                $promo = $layanan->promo;

                // Pastikan promo belum kadaluarsa (jika ada tanggal berakhir)
                if (!$promo->tanggal_berakhir || now()->lt($promo->tanggal_berakhir)) {
                    // Asumsi $promo->diskon adalah persentase (misal 10 untuk 10%)
                    $diskonPersen = $promo->diskon ?? 0;
                    $discount = floor($basePrice * ($diskonPersen / 100));
                    $promoName = $promo->nama_promo;
                }
            }
            // -----------------------------------------------------------------

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
                'discount' => (int) $discount, 
                'promo_name' => $promoName,
                'total_after_discount' => (int) $totalAfterDiscount, 
                'dp' => $dp,
                'remaining_payment' => (int) $remainingPayment, 
                'service_type' => $serviceType
            ]);
        } catch (\Exception $e) {
            Log::error('Error in calculateTotalCost: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menghitung biaya'], 500);
        }
    }

    public function store(Request $request)
    {
        // --- VALIDASI YANG DIPERBARUI ---
        $request->validate([
            'service_id' => 'required|exists:layanans,id',
            'time' => 'required',
            'tanggal' => 'required|date',
            'tipe_pembayaran' => 'required|in:dp,full',
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            // Pesan Kustom untuk Test Case
            // Case 1: Tidak memilih layanan/tanggal (diwakili oleh service_id/tanggal/time required)
            'service_id.required' => 'Silakan pilih layanan dan tanggal terlebih dahulu.',
            'time.required' => 'Silakan pilih layanan dan tanggal terlebih dahulu.',
            'tanggal.required' => 'Silakan pilih layanan dan tanggal terlebih dahulu.',

            // Case 4: Tanpa upload bukti pembayaran
            'bukti_transfer.required' => 'Bukti pembayaran wajib diunggah',

            // Case 2: Ukuran file melebihi batas
            'bukti_transfer.max' => 'Ukuran file maksimal 2MB',

            // Case 3: Format file tidak valid
            'bukti_transfer.mimes' => 'Format file tidak valid',
        ]);
        // ---------------------------------

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
                // Pastikan promo_id dimuat
                $query->select('id', 'nama', 'harga', 'deskripsi', 'gambar', 'tipe_layanan', 'promo_id');
            }, 'service.promo']) // <-- Eager load relasi Promo
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
                // Pastikan promo_id dimuat
                $query->select('id', 'nama', 'harga', 'deskripsi', 'gambar', 'tipe_layanan', 'promo_id');
            }, 'service.promo']) // <-- Eager load relasi Promo
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
                // Pastikan promo_id dimuat
                $query->select('id', 'nama', 'harga', 'deskripsi', 'gambar', 'tipe_layanan', 'promo_id');
            }, 'service.promo']) // <-- Eager load relasi Promo
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

    /**
     * Hitung informasi biaya untuk booking
     */
    private function calculateBookingCost($booking)
    {
        $basePrice = $booking->service->harga ?? 0;
        $discount = 0;
        $promoName = null;

        // --- LOGIKA PERHITUNGAN DISKON BARU (Menggunakan Relasi Promo) ---
        if ($booking->service && $booking->service->promo) {
            $promo = $booking->service->promo;

            // Cek tanggal berakhir promo (asumsi Promo model memiliki 'tanggal_berakhir')
            if (!$promo->tanggal_berakhir || now()->lt($promo->tanggal_berakhir)) {
                $diskonPersen = $promo->diskon ?? 0;
                // Hitung diskon dalam Rupiah
                $discount = floor($basePrice * ($diskonPersen / 100));
                $promoName = $promo->nama_promo;
            }
        }
        // -----------------------------------------------------------------

        $totalAfterDiscount = $basePrice - $discount;

        // Logika pembayaran DP
        $isFullPayment = $booking->tipe_pembayaran === 'full';
        $isDpConfirmed = $booking->status_dp === 'Lunas';
        $dp = $isFullPayment ? 0 : 50000; // DP tetap 50000 jika bukan full payment
        $remainingPayment = $isFullPayment ? 0 : ($totalAfterDiscount - $dp);

        return [
            'base_price' => $basePrice,
            'discount' => (int) $discount,
            'promo_name' => $promoName,
            'total_after_discount' => (int) $totalAfterDiscount,
            'dp' => $dp,
            'remaining_payment' => (int) $remainingPayment,
            'is_full_payment' => $isFullPayment,
            'is_dp_confirmed' => $isDpConfirmed,
        ];
    }
}

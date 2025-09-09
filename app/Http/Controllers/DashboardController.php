<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerBooking;
use App\Models\Layanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalPelanggan = CustomerBooking::distinct('customer_id')->count('customer_id');

        // === Statistik Utama Hari Ini ===
        $jumlahReservasiHariIni = CustomerBooking::whereDate('date', today())
            ->where('status', 'selesai')
            ->count();

        // Pendapatan hari ini - menggunakan join yang lebih efisien
        $pendapatanHariIni = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereDate('customer_bookings.date', today())
            ->where('customer_bookings.status', 'selesai')
            ->sum('layanans.harga');

        // === Mingguan ===
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $jumlahReservasiMingguan = CustomerBooking::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('status', 'selesai')
            ->count();

        $pendapatanMingguan = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereBetween('customer_bookings.date', [$startOfWeek, $endOfWeek])
            ->where('customer_bookings.status', 'selesai')
            ->sum('layanans.harga');

        // === Bulanan ===
        $jumlahReservasiBulanan = CustomerBooking::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->where('status', 'selesai')
            ->count();

        $pendapatanBulanan = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereMonth('customer_bookings.date', Carbon::now()->month)
            ->whereYear('customer_bookings.date', Carbon::now()->year)
            ->where('customer_bookings.status', 'selesai')
            ->sum('layanans.harga');

        // === Tahunan ===
        $jumlahReservasiTahunan = CustomerBooking::whereYear('date', Carbon::now()->year)
            ->where('status', 'selesai')
            ->count();

        $pendapatanTahunan = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereYear('customer_bookings.date', Carbon::now()->year)
            ->where('customer_bookings.status', 'selesai')
            ->sum('layanans.harga');

        $periode = $request->get('periode', 'mingguan');

        // === Data Grafik (7 hari terakhir) ===
        $tanggal = collect();
        $dataPendapatan = collect();
        $dataReservasi = collect();

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i);
            $formattedDay = $day->format('Y-m-d');

            $tanggal->push($day->format('d M'));

            // Pendapatan harian menggunakan join
            $pendapatanHarian = DB::table('customer_bookings')
                ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                ->whereDate('customer_bookings.date', $formattedDay)
                ->where('customer_bookings.status', 'selesai')
                ->sum('layanans.harga');

            $reservasi = CustomerBooking::whereDate('date', $formattedDay)
                ->where('status', 'selesai')
                ->count();

            $dataPendapatan->push($pendapatanHarian);
            $dataReservasi->push($reservasi);
        }

        return view('admin.dashboard', [
            // Statistik utama
            'jumlahReservasiHariIni'  => $jumlahReservasiHariIni,
            'pendapatanHariIni'       => $pendapatanHariIni,
            'totalPelanggan'          => $totalPelanggan,
            'jumlahReservasiMingguan' => $jumlahReservasiMingguan,
            'pendapatanMingguan'      => $pendapatanMingguan,
            'jumlahReservasiBulanan'  => $jumlahReservasiBulanan,
            'pendapatanBulanan'       => $pendapatanBulanan,
            'jumlahReservasiTahunan'  => $jumlahReservasiTahunan,
            'pendapatanTahunan'       => $pendapatanTahunan,
            'periode'                 => $periode,

            // Data grafik & tabel
            // 'labelPendapatan' => $tanggal,
            // 'dataPendapatan'  => $dataPendapatan,
            // 'labelReservasi'  => $tanggal,
            // 'dataReservasi'   => $dataReservasi,
            'labelPendapatan' => $tanggal->toArray(),
            'labelReservasi'  => $tanggal->toArray(),
            'dataPendapatan'  => $dataPendapatan->toArray(),
            'dataReservasi'   => $dataReservasi->toArray(),
        ]);
    }
}

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
        // === Statistik Utama Hari Ini ===
        $jumlahReservasiHariIni = CustomerBooking::whereDate('date', today())
            ->where('status', 'Selesai')
            ->count();

        $pendapatanHariIni = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereDate('customer_bookings.date', today())
            ->where('customer_bookings.status', 'Selesai')
            ->sum('layanans.harga');

        $totalPelangganHariIni = CustomerBooking::whereDate('date', today())
            ->where('status', 'Selesai')
            ->distinct('customer_id')
            ->count('customer_id');

        // === Mingguan ===
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $jumlahReservasiMingguan = CustomerBooking::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('status', 'Selesai')
            ->count();

        $pendapatanMingguan = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereBetween('customer_bookings.date', [$startOfWeek, $endOfWeek])
            ->where('customer_bookings.status', 'Selesai')
            ->sum('layanans.harga');

        $jumlahPelangganMingguan = CustomerBooking::whereBetween('date', [$startOfWeek, $endOfWeek])
            ->where('status', 'Selesai')
            ->distinct('customer_id')
            ->count('customer_id');

        // === Bulanan ===
        $jumlahReservasiBulanan = CustomerBooking::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->where('status', 'Selesai')
            ->count();

        $pendapatanBulanan = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereMonth('customer_bookings.date', Carbon::now()->month)
            ->whereYear('customer_bookings.date', Carbon::now()->year)
            ->where('customer_bookings.status', 'Selesai')
            ->sum('layanans.harga');

        $jumlahPelangganBulanan = CustomerBooking::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->where('status', 'Selesai')
            ->distinct('customer_id')
            ->count('customer_id');

        // === Tahunan ===
        $jumlahReservasiTahunan = CustomerBooking::whereYear('date', Carbon::now()->year)
            ->where('status', 'Selesai')
            ->count();

        $pendapatanTahunan = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereYear('customer_bookings.date', Carbon::now()->year)
            ->where('customer_bookings.status', 'Selesai')
            ->sum('layanans.harga');

        $jumlahPelangganTahunan = CustomerBooking::whereYear('date', Carbon::now()->year)
            ->where('status', 'Selesai')
            ->distinct('customer_id')
            ->count('customer_id');

        $periode = $request->get('periode', 'mingguan');

        // === Data Grafik berdasarkan periode ===
        $tanggal = collect();
        $dataPendapatan = collect();
        $dataReservasi = collect();
        $dataPelanggan = collect();

        if ($periode == 'mingguan') {
            for ($i = 6; $i >= 0; $i--) {
                $day = Carbon::now()->subDays($i);
                $formattedDay = $day->format('Y-m-d');

                $tanggal->push($day->format('d M'));

                $pendapatanHarian = DB::table('customer_bookings')
                    ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                    ->whereDate('customer_bookings.date', $formattedDay)
                    ->where('customer_bookings.status', 'Selesai')
                    ->sum('layanans.harga');

                $reservasi = CustomerBooking::whereDate('date', $formattedDay)
                    ->where('status', 'Selesai')
                    ->count();

                $pelanggan = CustomerBooking::whereDate('date', $formattedDay)
                    ->where('status', 'Selesai')
                    ->distinct('customer_id')
                    ->count('customer_id');

                $dataPendapatan->push($pendapatanHarian);
                $dataReservasi->push($reservasi);
                $dataPelanggan->push($pelanggan);
            }
        } elseif ($periode == 'bulanan') {
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);

                $tanggal->push($month->format('M Y'));

                $pendapatanBulanan = DB::table('customer_bookings')
                    ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                    ->whereMonth('customer_bookings.date', $month->month)
                    ->whereYear('customer_bookings.date', $month->year)
                    ->where('customer_bookings.status', 'Selesai')
                    ->sum('layanans.harga');

                $reservasi = CustomerBooking::whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->where('status', 'Selesai')
                    ->count();

                $pelanggan = CustomerBooking::whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->where('status', 'Selesai')
                    ->distinct('customer_id')
                    ->count('customer_id');

                $dataPendapatan->push($pendapatanBulanan);
                $dataReservasi->push($reservasi);
                $dataPelanggan->push($pelanggan);
            }
        } else { // tahunan
            for ($i = 4; $i >= 0; $i--) {
                $year = Carbon::now()->subYears($i);

                $tanggal->push($year->format('Y'));

                $pendapatanTahunan = DB::table('customer_bookings')
                    ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                    ->whereYear('customer_bookings.date', $year->year)
                    ->where('customer_bookings.status', 'Selesai')
                    ->sum('layanans.harga');

                $reservasi = CustomerBooking::whereYear('date', $year->year)
                    ->where('status', 'Selesai')
                    ->count();

                $pelanggan = CustomerBooking::whereYear('date', $year->year)
                    ->where('status', 'Selesai')
                    ->distinct('customer_id')
                    ->count('customer_id');

                $dataPendapatan->push($pendapatanTahunan);
                $dataReservasi->push($reservasi);
                $dataPelanggan->push($pelanggan);
            }
        }

        return view('admin.dashboard', [
            'jumlahReservasiHariIni'   => $jumlahReservasiHariIni,
            'pendapatanHariIni'        => $pendapatanHariIni,
            'totalPelangganHariIni'    => $totalPelangganHariIni,

            'jumlahReservasiMingguan'  => $jumlahReservasiMingguan,
            'pendapatanMingguan'       => $pendapatanMingguan,
            'jumlahPelangganMingguan'  => $jumlahPelangganMingguan,

            'jumlahReservasiBulanan'   => $jumlahReservasiBulanan,
            'pendapatanBulanan'        => $pendapatanBulanan,
            'jumlahPelangganBulanan'   => $jumlahPelangganBulanan,

            'jumlahReservasiTahunan'   => $jumlahReservasiTahunan,
            'pendapatanTahunan'        => $pendapatanTahunan,
            'jumlahPelangganTahunan'   => $jumlahPelangganTahunan,

            'periode'                  => $periode,

            'labelPendapatan' => $tanggal->toArray(),
            'labelReservasi'  => $tanggal->toArray(),
            'labelPelanggan'  => $tanggal->toArray(),
            'dataPendapatan'  => $dataPendapatan->toArray(),
            'dataReservasi'   => $dataReservasi->toArray(),
            'dataPelanggan'   => $dataPelanggan->toArray(),
        ]);
    }
}

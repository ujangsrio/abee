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
            ->where('status', 'selesai')
            ->count();

        // Pendapatan hari ini - menggunakan join yang lebih efisien
        $pendapatanHariIni = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereDate('customer_bookings.date', today())
            ->where('customer_bookings.status', 'selesai')
            ->sum('layanans.harga');

        // Total pelanggan hari ini - pelanggan yang melakukan booking hari ini
        $totalPelangganHariIni = CustomerBooking::whereDate('date', today())
            ->where('status', 'selesai')
            ->distinct('customer_id')
            ->count('customer_id');

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

        // === Data Grafik berdasarkan periode ===
        $tanggal = collect();
        $dataPendapatan = collect();
        $dataReservasi = collect();
        $dataPelanggan = collect();

        if ($periode == 'mingguan') {
            // Data 7 hari terakhir
            for ($i = 6; $i >= 0; $i--) {
                $day = Carbon::now()->subDays($i);
                $formattedDay = $day->format('Y-m-d');

                $tanggal->push($day->format('d M'));

                // Pendapatan harian
                $pendapatanHarian = DB::table('customer_bookings')
                    ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                    ->whereDate('customer_bookings.date', $formattedDay)
                    ->where('customer_bookings.status', 'selesai')
                    ->sum('layanans.harga');

                // Reservasi harian
                $reservasi = CustomerBooking::whereDate('date', $formattedDay)
                    ->where('status', 'selesai')
                    ->count();

                // Pelanggan harian
                $pelanggan = CustomerBooking::whereDate('date', $formattedDay)
                    ->where('status', 'selesai')
                    ->distinct('customer_id')
                    ->count('customer_id');

                $dataPendapatan->push($pendapatanHarian);
                $dataReservasi->push($reservasi);
                $dataPelanggan->push($pelanggan);
            }
        } elseif ($periode == 'bulanan') {
            // Data 12 bulan terakhir
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);

                $tanggal->push($month->format('M Y'));

                // Pendapatan bulanan
                $pendapatanBulanan = DB::table('customer_bookings')
                    ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                    ->whereMonth('customer_bookings.date', $month->month)
                    ->whereYear('customer_bookings.date', $month->year)
                    ->where('customer_bookings.status', 'selesai')
                    ->sum('layanans.harga');

                // Reservasi bulanan
                $reservasi = CustomerBooking::whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->where('status', 'selesai')
                    ->count();

                // Pelanggan bulanan
                $pelanggan = CustomerBooking::whereMonth('date', $month->month)
                    ->whereYear('date', $month->year)
                    ->where('status', 'selesai')
                    ->distinct('customer_id')
                    ->count('customer_id');

                $dataPendapatan->push($pendapatanBulanan);
                $dataReservasi->push($reservasi);
                $dataPelanggan->push($pelanggan);
            }
        } else { // tahunan
            // Data 5 tahun terakhir
            for ($i = 4; $i >= 0; $i--) {
                $year = Carbon::now()->subYears($i);

                $tanggal->push($year->format('Y'));

                // Pendapatan tahunan
                $pendapatanTahunan = DB::table('customer_bookings')
                    ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                    ->whereYear('customer_bookings.date', $year->year)
                    ->where('customer_bookings.status', 'selesai')
                    ->sum('layanans.harga');

                // Reservasi tahunan
                $reservasi = CustomerBooking::whereYear('date', $year->year)
                    ->where('status', 'selesai')
                    ->count();

                // Pelanggan tahunan
                $pelanggan = CustomerBooking::whereYear('date', $year->year)
                    ->where('status', 'selesai')
                    ->distinct('customer_id')
                    ->count('customer_id');

                $dataPendapatan->push($pendapatanTahunan);
                $dataReservasi->push($reservasi);
                $dataPelanggan->push($pelanggan);
            }
        }

        return view('admin.dashboard', [
            // Statistik utama
            'jumlahReservasiHariIni'  => $jumlahReservasiHariIni,
            'pendapatanHariIni'       => $pendapatanHariIni,
            'totalPelangganHariIni'   => $totalPelangganHariIni,
            'jumlahReservasiMingguan' => $jumlahReservasiMingguan,
            'pendapatanMingguan'      => $pendapatanMingguan,
            'jumlahReservasiBulanan'  => $jumlahReservasiBulanan,
            'pendapatanBulanan'       => $pendapatanBulanan,
            'jumlahReservasiTahunan'  => $jumlahReservasiTahunan,
            'pendapatanTahunan'       => $pendapatanTahunan,
            'periode'                 => $periode,

            // Data grafik berdasarkan periode
            'labelPendapatan' => $tanggal->toArray(),
            'labelReservasi'  => $tanggal->toArray(),
            'labelPelanggan'  => $tanggal->toArray(),
            'dataPendapatan'  => $dataPendapatan->toArray(),
            'dataReservasi'   => $dataReservasi->toArray(),
            'dataPelanggan'   => $dataPelanggan->toArray(),
        ]);
    }
}

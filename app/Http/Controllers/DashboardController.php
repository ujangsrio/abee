<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerBooking;
use App\Models\Customer;
use App\Models\Layanan;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Statistik atas
        $jumlahReservasiHariIni = CustomerBooking::whereDate('date', today())->count();
        $pendapatanHariIni = CustomerBooking::whereDate('date', today())->count() * 50000; // asumsi DP 50rb
        $totalPelanggan = Customer::count();


        // Ambil periode dari dropdown (default: mingguan)
        $periode = $request->get('periode', 'mingguan');

        $labelPendapatan = [];
        $dataPendapatan = [];
        $labelReservasi = [];
        $dataReservasi = [];

        switch ($periode) {
            case 'bulanan':
                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $labelPendapatan[] = Carbon::create()->month($bulan)->translatedFormat('F');
                    $dataPendapatan[] = CustomerBooking::whereMonth('date', $bulan)
                        ->whereYear('date', now()->year)
                        ->count() * 50000;

                    $labelReservasi[] = Carbon::create()->month($bulan)->translatedFormat('F');
                    $dataReservasi[] = CustomerBooking::whereMonth('date', $bulan)
                        ->whereYear('date', now()->year)
                        ->count();
                }
                break;

            case 'tahunan':
                $startYear = now()->year - 3;
                for ($tahun = $startYear; $tahun <= now()->year; $tahun++) {
                    $labelPendapatan[] = $tahun;
                    $dataPendapatan[] = CustomerBooking::whereYear('date', $tahun)->count() * 50000;

                    $labelReservasi[] = $tahun;
                    $dataReservasi[] = CustomerBooking::whereYear('date', $tahun)->count();
                }
                break;

            default: // mingguan
                foreach (range(6, 0) as $i) {
                    $tanggal = Carbon::now()->subDays($i)->format('Y-m-d');
                    $labelPendapatan[] = Carbon::parse($tanggal)->translatedFormat('l');
                    $dataPendapatan[] = CustomerBooking::whereDate('date', $tanggal)->count() * 50000;

                    $labelReservasi[] = Carbon::parse($tanggal)->translatedFormat('l');
                    $dataReservasi[] = CustomerBooking::whereDate('date', $tanggal)->count();
                }
                break;
        }

        return view('admin.dashboard', [
            'jumlahReservasiHariIni' => $jumlahReservasiHariIni,
            'pendapatanHariIni' => $pendapatanHariIni,
            'totalPelanggan' => $totalPelanggan,
            'labelPendapatan' => $labelPendapatan,
            'dataPendapatan' => $dataPendapatan,
            'labelReservasi' => $labelReservasi,
            'dataReservasi' => $dataReservasi,
            'periode' => $periode
        ]);
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\CustomerBooking;
use App\Models\Layanan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();

        // Reservasi Hari Ini (semua status)
        $reservasiHariIni = CustomerBooking::whereDate('date', $today)->count();

        // Pendapatan Hari Ini - HANYA dari booking dengan status 'Selesai'
        $pendapatanHariIni = DB::table('customer_bookings')
            ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->whereDate('customer_bookings.date', $today)
            ->where('customer_bookings.status', 'Selesai')
            ->sum('layanans.harga');

        // Pendapatan bulan ini dari booking yang selesai
        // $pendapatanBulanIni = DB::table('customer_bookings')
        //     ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
        //     ->whereYear('customer_bookings.date', $today->year)
        //     ->whereMonth('customer_bookings.date', $today->month)
        //     ->where('customer_bookings.status', 'Selesai')
        //     ->sum('layanans.harga');

        // Pelanggan baru hari ini
        $pelangganHariIni = Customer::whereDate('created_at', $today)->count();

        // Total pelanggan
        $pelangganTotal = Customer::count();

        // Booking selesai hari ini
        $bookingSelesaiHariIni = CustomerBooking::whereDate('date', $today)
            ->where('status', 'Selesai')
            ->count();

        return [
            Stat::make('Reservasi Hari Ini', $reservasiHariIni)
                ->description('Total reservasi untuk hari ini')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning'),

            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'))
                ->description('Dari ' . $bookingSelesaiHariIni . ' layanan selesai')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            // Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
            //     ->description('Total pendapatan bulan ' . $today->month)
            //     ->descriptionIcon('heroicon-m-banknotes')
            //     ->color('primary'),

            Stat::make('Pelanggan Baru', $pelangganHariIni)
                ->description('Dari total ' . $pelangganTotal . ' pelanggan')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}

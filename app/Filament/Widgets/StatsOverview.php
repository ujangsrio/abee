<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\CustomerBooking;
use App\Models\CustomerLayanan;
use App\Models\Layanan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    private function calculateRevenue($bookings): float
    {
        $pendapatan = 0;
        foreach ($bookings as $booking) {
            $layanan = Layanan::find($booking->service_id);
            if ($layanan) {
                $pendapatan += $layanan->harga;
            }
        }
        return $pendapatan;
    }
    protected function getStats(): array
    {
        $today = Carbon::today();
        $reservasiHariIni = CustomerBooking::whereDate('date', $today)->count();

        $pendapatanHariIni = CustomerBooking::whereDate('date', $today)
            ->whereIn('status', ['Dikonfirmasi', 'Selesai'])
            ->get()
            ->sum(function ($booking) {
                $layanan = CustomerLayanan::find($booking->service_id);
                return $layanan ? $layanan->harga : 0;
            });

        $pelangganHariIni = Customer::whereDate('created_at', $today)->count();
        $pelangganTotal = Customer::count();

        $totalBookings = CustomerBooking::count();
        $totalBookingSelesai = CustomerBooking::where('status', 'Selesai')->count();
        $totalPendapatan = $this->calculateRevenue(CustomerBooking::where('status', 'Selesai')->get());

        return [
            Stat::make('Reservasi Hari Ini', $reservasiHariIni)
                ->description('Total reservasi untuk hari ini')
                ->descriptionIcon('heroicon-m-calendar')
                // ->chart([7, 2, 10, 3, 15, 4, 15])
                ->color('warning'),

            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'))
                ->description('Total pendapatan hari ini')
                ->descriptionIcon('heroicon-m-currency-dollar')
                // ->chart([7, 2, 10, 3, 15, 4, 20])
                ->color('success'),

            Stat::make('Pelanggan Baru Hari Ini', $pelangganHariIni)
                ->description('Pelanggan baru hari ini')
                ->descriptionIcon('heroicon-m-users')
                // ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info'),

        ];
    }
}

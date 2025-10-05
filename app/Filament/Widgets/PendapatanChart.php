<?php

namespace App\Filament\Widgets;

use App\Models\CustomerBooking;
use App\Models\CustomerLayanan;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PendapatanChart extends ChartWidget
{
    protected static ?string $heading = 'Total Pendapatan';
    protected static ?int $sort = 2;


    public ?string $filter = 'daily';

    protected function getFilters(): ?array
    {
        return [
            'daily' => 'Harian',
            'weekly' => 'Mingguan',
            'monthly' => 'Bulanan',
            'yearly' => 'Tahunan',
        ];
    }

    protected function getData(): array
    {
        $filter = $this->filter;

        switch ($filter) {
            case 'daily':
                return $this->getDailyData();
            case 'weekly':
                return $this->getWeeklyData();
            case 'monthly':
                return $this->getMonthlyData();
            case 'yearly':
                return $this->getYearlyData();
            default:
                return $this->getDailyData();
        }
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getDailyData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');

            $revenue = CustomerBooking::whereDate('date', $date)
                ->whereIn('status', ['Dikonfirmasi', 'Selesai'])
                ->get()
                ->sum(function ($booking) {
                    $layanan = CustomerLayanan::find($booking->service_id);
                    return $layanan ? $layanan->harga : 0;
                });

            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getWeeklyData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            $labels[] = 'Minggu ' . $startOfWeek->weekOfYear;

            $revenue = CustomerBooking::whereBetween('date', [$startOfWeek, $endOfWeek])
                ->whereIn('status', ['Dikonfirmasi', 'Selesai'])
                ->get()
                ->sum(function ($booking) {
                    $layanan = CustomerLayanan::find($booking->service_id);
                    return $layanan ? $layanan->harga : 0;
                });

            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getMonthlyData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $revenue = CustomerBooking::whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->whereIn('status', ['Dikonfirmasi', 'Selesai'])
                ->get()
                ->sum(function ($booking) {
                    $layanan = CustomerLayanan::find($booking->service_id);
                    return $layanan ? $layanan->harga : 0;
                });

            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    private function getYearlyData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 2; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $labels[] = $year;

            $revenue = CustomerBooking::whereYear('date', $year)
                ->whereIn('status', ['Dikonfirmasi', 'Selesai'])
                ->get()
                ->sum(function ($booking) {
                    $layanan = CustomerLayanan::find($booking->service_id);
                    return $layanan ? $layanan->harga : 0;
                });

            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }
}

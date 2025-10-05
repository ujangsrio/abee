<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PelangganChart extends ChartWidget
{
    protected static ?string $heading = 'Total Pelanggan Baru';
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
            $data[] = Customer::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pelanggan Baru',
                    'data' => $data,
                    'borderColor' => 'rgb(168, 85, 247)',
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
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
            $data[] = Customer::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pelanggan Baru',
                    'data' => $data,
                    'borderColor' => 'rgb(168, 85, 247)',
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
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
            $data[] = Customer::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pelanggan Baru',
                    'data' => $data,
                    'borderColor' => 'rgb(168, 85, 247)',
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
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
            $data[] = Customer::whereYear('created_at', $year)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pelanggan Baru',
                    'data' => $data,
                    'borderColor' => 'rgb(168, 85, 247)',
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }
}

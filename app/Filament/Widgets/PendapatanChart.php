<?php

namespace App\Filament\Widgets;

use App\Models\CustomerBooking;
use App\Models\Layanan;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PendapatanChart extends ChartWidget
{
    protected static ?string $heading = 'Pendapatan';
    protected static ?int $sort = 3;

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

            $pendapatan = DB::table('customer_bookings')
                ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                ->whereDate('customer_bookings.date', $date->format('Y-m-d'))
                ->where('customer_bookings.status', 'Selesai')
                ->sum('layanans.harga');

            $data[] = $pendapatan;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
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

            $pendapatan = DB::table('customer_bookings')
                ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                ->whereBetween('customer_bookings.date', [
                    $startOfWeek->format('Y-m-d'),
                    $endOfWeek->format('Y-m-d')
                ])
                ->where('customer_bookings.status', 'Selesai')
                ->sum('layanans.harga');

            $data[] = $pendapatan;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
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

            $pendapatan = DB::table('customer_bookings')
                ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                ->whereYear('customer_bookings.date', $month->year)
                ->whereMonth('customer_bookings.date', $month->month)
                ->where('customer_bookings.status', 'Selesai')
                ->sum('layanans.harga');

            $data[] = $pendapatan;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
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

            $pendapatan = DB::table('customer_bookings')
                ->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                ->whereYear('customer_bookings.date', $year)
                ->where('customer_bookings.status', 'Selesai')
                ->sum('layanans.harga');

            $data[] = $pendapatan;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $data,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'drawBorder' => false,
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { 
                            if (value >= 1000000) {
                                return "Rp " + (value / 1000000).toFixed(1) + " jt";
                            } else if (value >= 1000) {
                                return "Rp " + (value / 1000).toFixed(0) + " rb";
                            }
                            return "Rp " + value; 
                        }'
                    ]
                ],
                'x' => [
                    'grid' => [
                        'display' => false,
                    ]
                ]
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { 
                            return "Rp " + context.parsed.y.toLocaleString("id-ID"); 
                        }'
                    ]
                ]
            ],
            'maintainAspectRatio' => false,
            'responsive' => true,
        ];
    }

    // public function getDescription(): ?string
    // {
    //     return 'Grafik pendapatan dari layanan yang sudah selesai';
    // }
}

@php
    $periods = [
        'today' => 'Hari Ini',
        'week' => 'Minggu Ini', 
        'month' => 'Bulan Ini',
        'year' => 'Tahun Ini',
        'all' => 'Semua Waktu'
    ];
@endphp

<x-filament-widgets::widget class="fi-stats-overview-widget">
    <x-filament::section>
        <!-- Header dengan Filter -->
        <div class="flex flex-col gap-4 mb-6">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-950 dark:text-white">
                    Statistik Ringkas
                </h2>
                
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    Periode: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $periods[$this->selectedPeriod] ?? 'Hari Ini' }}</span>
                </div>
            </div>

            <!-- Tombol Filter -->
            <div class="flex flex-wrap gap-2">
                @foreach($periods as $key => $label)
                    <button 
                        wire:click="$set('selectedPeriod', '{{ $key }}')"
                        class="px-3 py-2 text-xs font-medium rounded-lg transition-colors {{ $this->selectedPeriod === $key 
                            ? 'bg-primary-500 text-white shadow-sm' 
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700' 
                        }}"
                    >
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-4">
            @foreach ($this->getCachedStats() as $stat)
                <div class="relative p-6 bg-white rounded-xl border border-gray-200 dark:bg-gray-800 dark:border-gray-700 shadow-sm">
                    <!-- Trend Indicator -->
                    @if(str_contains($stat->description, '↑') || str_contains($stat->description, '↓'))
                        <div class="absolute top-4 right-4">
                            @if(str_contains($stat->description, '↑'))
                                <div class="flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium dark:bg-green-900 dark:text-green-300">
                                    <x-heroicon-s-arrow-trending-up class="w-3 h-3" />
                                    Trend Naik
                                </div>
                            @else
                                <div class="flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium dark:bg-red-900 dark:text-red-300">
                                    <x-heroicon-s-arrow-trending-down class="w-3 h-3" />
                                    Trend Turun
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Stat Content -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="p-2 rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                                @if($stat->color === 'success')
                                    <x-heroicon-s-currency-dollar class="w-5 h-5" />
                                @elseif($stat->color === 'warning')
                                    <x-heroicon-s-calendar class="w-5 h-5" />
                                @elseif($stat->color === 'info')
                                    <x-heroicon-s-users class="w-5 h-5" />
                                @else
                                    <x-heroicon-s-chart-bar class="w-5 h-5" />
                                @endif
                            </div>
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $stat->label }}
                            </div>
                        </div>

                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $stat->value }}
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            @if(str_contains($stat->description, '↑'))
                                <x-heroicon-s-arrow-trending-up class="w-4 h-4 text-green-500" />
                            @elseif(str_contains($stat->description, '↓'))
                                <x-heroicon-s-arrow-trending-down class="w-4 h-4 text-red-500" />
                            @else
                                <x-heroicon-s-minus class="w-4 h-4 text-gray-500" />
                            @endif
                            <span>{{ $stat->description }}</span>
                        </div>

                        <!-- Mini Chart -->
                        @if(!empty($stat->chart))
                            <div class="pt-2">
                                <div class="h-12">
                                    <canvas 
                                        x-data="{
                                            chart: null,
                                            init() {
                                                const ctx = this.$el.getContext('2d')
                                                this.chart = new Chart(ctx, {
                                                    type: 'line',
                                                    data: {
                                                        labels: Array({{ count($stat->chart) }}).fill(''),
                                                        datasets: [{
                                                            data: {{ json_encode($stat->chart) }},
                                                            borderColor: '{{ $stat->color === 'success' ? '#10b981' : ($stat->color === 'warning' ? '#f59e0b' : ($stat->color === 'info' ? '#3b82f6' : '#6b7280')) }}',
                                                            backgroundColor: '{{ $stat->color === 'success' ? '#10b98120' : ($stat->color === 'warning' ? '#f59e0b20' : ($stat->color === 'info' ? '#3b82f620' : '#6b728020')) }}',
                                                            borderWidth: 2,
                                                            fill: true,
                                                            tension: 0.4,
                                                            pointRadius: 0
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false,
                                                        plugins: { legend: { display: false } },
                                                        scales: {
                                                            x: { display: false },
                                                            y: { display: false }
                                                        }
                                                    }
                                                })
                                            }
                                        }"
                                        wire:ignore
                                    ></canvas>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endassets
</x-filament-widgets::widget>
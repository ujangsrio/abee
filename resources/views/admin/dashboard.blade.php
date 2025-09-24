@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <div class="max-w-6xl mx-auto">
        
        {{-- Debug Data (Bisa dihapus setelah fix) --}}
        <div class="bg-yellow-100 border border-yellow-400 p-4 rounded mb-6" style="display: none;">
            <h4 class="font-bold">Debug Data:</h4>
            <p>Label: {{ json_encode($labelPendapatan) }}</p>
            <p>Data Pendapatan: {{ json_encode($dataPendapatan) }}</p>
            <p>Data Reservasi: {{ json_encode($dataReservasi) }}</p>
        </div>

        {{-- Header & Logout --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard Admin - Aretha Beauty</h2>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button 
                    type="submit"
                    onclick="return confirm('Apakah Anda yakin ingin logout?')"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition"
                >
                    Logout
                </button>
            </form>
        </div> 

        {{-- Statistik --}}
        <div class="overflow-x-auto mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 min-w-[640px]">
                <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow">
                    <p class="text-sm text-gray-500">Reservasi Hari Ini</p>
                    <p class="text-2xl font-bold text-purple-800 mt-1">{{ $jumlahReservasiHariIni }}</p>
                </div>
                <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow">
                    <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-bold text-purple-800 mt-1">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow">
                    <p class="text-sm text-gray-500">Total Pelanggan Hari Ini</p>
                    <p class="text-2xl font-bold text-purple-800 mt-1">{{ $totalPelangganHariIni }}</p>
                </div>
            </div>
        </div>

        {{-- Filter Periode --}}
        <div class="flex justify-end mb-6">
            <form method="GET">
                <label class="mr-2 text-gray-600 font-medium">Periode:</label>
                <select name="periode" onchange="this.form.submit()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-purple-400 focus:border-purple-400">
                    <option value="mingguan" {{ $periode == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ $periode == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    <option value="tahunan" {{ $periode == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </form>
        </div>

        {{-- Grid untuk Pie Charts --}}
        

        {{-- Grafik Batang --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            {{-- Grafik Reservasi --}}
            <div class="bg-white border border-purple-100 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-purple-800 mb-4">Grafik Reservasi</h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="reservasiBarChart"></canvas>
                </div>
            </div>

            {{-- Grafik Pendapatan --}}
            <div class="bg-white border border-purple-100 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-purple-800 mb-4">Grafik Pendapatan</h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="pendapatanBarChart"></canvas>
                </div>
            </div>

            {{-- Grafik Pelanggan --}}
            <div class="bg-white border border-purple-100 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-purple-800 mb-4">Grafik Pelanggan</h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="pelangganBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fungsi untuk inisialisasi bar charts
    function initializeBarCharts() {
        console.log('Initializing bar charts...');
        
        // Data dari controller
        const labels = @json($labelPendapatan ?? []);
        const dataPendapatan = @json($dataPendapatan ?? []);
        const dataReservasi = @json($dataReservasi ?? []);
        const dataPelanggan = @json($dataPelanggan ?? []);

        console.log('Bar chart data:', {
            labels: labels,
            pendapatan: dataPendapatan,
            reservasi: dataReservasi,
            pelanggan: dataPelanggan
        });

        // Konfigurasi umum untuk bar charts
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        };

        // Bar Chart Reservasi
        const reservasiBarChart = document.getElementById('reservasiBarChart');
        if (reservasiBarChart && dataReservasi.length > 0) {
            try {
                new Chart(reservasiBarChart, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataReservasi,
                            backgroundColor: '#a855f7',
                            borderColor: '#7c3aed',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.raw} reservasi`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Reservasi bar chart created successfully');
            } catch (error) {
                console.error('Error creating reservasi bar chart:', error);
            }
        }

        // Bar Chart Pendapatan
        const pendapatanBarChart = document.getElementById('pendapatanBarChart');
        if (pendapatanBarChart && dataPendapatan.length > 0) {
            try {
                new Chart(pendapatanBarChart, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataPendapatan,
                            backgroundColor: '#10b981',
                            borderColor: '#059669',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Rp ${context.raw.toLocaleString('id-ID')}`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Pendapatan bar chart created successfully');
            } catch (error) {
                console.error('Error creating pendapatan bar chart:', error);
            }
        }

        // Bar Chart Pelanggan
        const pelangganBarChart = document.getElementById('pelangganBarChart');
        if (pelangganBarChart && dataPelanggan.length > 0) {
            try {
                new Chart(pelangganBarChart, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataPelanggan,
                            backgroundColor: '#3b82f6',
                            borderColor: '#2563eb',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.raw} pelanggan`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Pelanggan bar chart created successfully');
            } catch (error) {
                console.error('Error creating pelanggan bar chart:', error);
            }
        }
    }

    // Tunggu sampai DOM siap dan Chart.js terload
    if (typeof Chart !== 'undefined') {
        document.addEventListener('DOMContentLoaded', initializeBarCharts);
    } else {
        console.error('Chart.js not loaded');
        
        // Coba load ulang Chart.js jika gagal
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
        script.onload = initializeBarCharts;
        document.head.appendChild(script);
    }
</script>
@endsection
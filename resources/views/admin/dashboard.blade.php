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
                    <p class="text-sm text-gray-500">Total Pelanggan</p>
                    <p class="text-2xl font-bold text-purple-800 mt-1">{{ $totalPelanggan }}</p>
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
        

        {{-- Tabel Rekap Pemasukan --}}
        <div class="bg-white border border-purple-100 rounded-2xl shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-purple-800 mb-4">Tabel Reservasi</h3>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-200 rounded-lg text-sm">
                    <thead>
                        <tr class="bg-purple-50 text-gray-700">
                            <th class="px-4 py-2 border">Tanggal</th>
                            <th class="px-4 py-2 border">Pendapatan</th>
                            <th class="px-4 py-2 border">Jumlah Reservasi</th>
                            <th class="px-4 py-2 border">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($labelPendapatan) > 0)
                            @php
                                $totalPendapatan = array_sum($dataPendapatan);
                                $totalReservasi = array_sum($dataReservasi);
                            @endphp
                            @foreach($labelPendapatan as $index => $label)
                                @php
                                    $pendapatan = $dataPendapatan[$index] ?? 0;
                                    $reservasi = $dataReservasi[$index] ?? 0;
                                    $percentagePendapatan = $totalPendapatan > 0 ? round(($pendapatan / $totalPendapatan) * 100, 1) : 0;
                                    $percentageReservasi = $totalReservasi > 0 ? round(($reservasi / $totalReservasi) * 100, 1) : 0;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $label }}</td>
                                    <td class="px-4 py-2 border">Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 border">{{ $reservasi }}</td>
                                    <td class="px-4 py-2 border">
                                        <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">
                                            {{ $percentagePendapatan }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            {{-- Total Row --}}
                            <tr class="bg-purple-50 font-semibold">
                                <td class="px-4 py-2 border">TOTAL</td>
                                <td class="px-4 py-2 border">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 border">{{ $totalReservasi }}</td>
                                <td class="px-4 py-2 border">100%</td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="4" class="px-4 py-2 border text-center text-gray-500">
                                    Tidak ada data untuk ditampilkan
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fungsi untuk inisialisasi pie charts
    function initializePieCharts() {
        console.log('Initializing pie charts...');
        
        // Data dari controller
        const labelPendapatan = @json($labelPendapatan ?? []);
        const dataPendapatan = @json($dataPendapatan ?? []);
        const dataReservasi = @json($dataReservasi ?? []);

        console.log('Pie chart data:', {
            labels: labelPendapatan,
            pendapatan: dataPendapatan,
            reservasi: dataReservasi
        });

        // Warna untuk pie chart
        const colorPalette = [
            '#a855f7', '#ec4899', '#f59e0b', '#10b981', '#3b82f6', 
            '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#84cc16'
        ];

        // Pie Chart Pendapatan
        const pendapatanPieChart = document.getElementById('pendapatanPieChart');
        if (pendapatanPieChart && dataPendapatan.length > 0) {
            try {
                new Chart(pendapatanPieChart, {
                    type: 'pie',
                    data: {
                        labels: labelPendapatan,
                        datasets: [{
                            data: dataPendapatan,
                            backgroundColor: colorPalette,
                            borderColor: '#fff',
                            borderWidth: 2,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: Rp ${value.toLocaleString('id-ID')} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Pendapatan pie chart created successfully');
            } catch (error) {
                console.error('Error creating pendapatan pie chart:', error);
            }
        }

        // Pie Chart Reservasi
        const reservasiPieChart = document.getElementById('reservasiPieChart');
        if (reservasiPieChart && dataReservasi.length > 0) {
            try {
                new Chart(reservasiPieChart, {
                    type: 'pie',
                    data: {
                        labels: labelPendapatan,
                        datasets: [{
                            data: dataReservasi,
                            backgroundColor: colorPalette,
                            borderColor: '#fff',
                            borderWidth: 2,
                            hoverOffset: 15
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return `${label}: ${value} reservasi (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Reservasi pie chart created successfully');
            } catch (error) {
                console.error('Error creating reservasi pie chart:', error);
            }
        }
    }

    // Tunggu sampai DOM siap dan Chart.js terload
    if (typeof Chart !== 'undefined') {
        document.addEventListener('DOMContentLoaded', initializePieCharts);
    } else {
        console.error('Chart.js not loaded');
        
        // Coba load ulang Chart.js jika gagal
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
        script.onload = initializePieCharts;
        document.head.appendChild(script);
    }
</script>
@endsection
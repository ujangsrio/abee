@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <div class="max-w-6xl mx-auto">
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

        {{-- Grafik Pendapatan --}}
        <div class="bg-white border border-purple-100 rounded-2xl shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-purple-800 mb-4">Grafik Pendapatan</h3>
            <div class="overflow-x-auto">
                <canvas id="pendapatanChart"></canvas>
            </div>
        </div>

        {{-- Grafik Reservasi --}}
        <div class="bg-white border border-purple-100 rounded-2xl shadow p-6">
            <h3 class="text-lg font-semibold text-purple-800 mb-4">Jumlah Reservasi</h3>
            <div class="overflow-x-auto">
                <canvas id="reservasiChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labelPendapatan = @json($labelPendapatan);
    const dataPendapatan = @json($dataPendapatan);
    const labelReservasi = @json($labelReservasi);
    const dataReservasi = @json($dataReservasi);

    new Chart(document.getElementById('pendapatanChart'), {
        type: 'line',
        data: {
            labels: labelPendapatan,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: dataPendapatan,
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                borderColor: '#a855f7',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#a855f7'
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }},
            scales: { y: { beginAtZero: true } }
        }
    });

    new Chart(document.getElementById('reservasiChart'), {
        type: 'bar',
        data: {
            labels: labelReservasi,
            datasets: [{
                label: 'Jumlah Reservasi',
                data: dataReservasi,
                backgroundColor: '#a855f7',
                borderRadius: 8,
                barThickness: 30
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false }},
            scales: { y: { beginAtZero: true, precision: 0 } }
        }
    });
</script>
@endsection

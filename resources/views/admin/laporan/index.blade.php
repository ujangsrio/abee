@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Laporan Reservasi - Aretha Beauty
    </h1>

    <!-- Tombol Export -->
    <div class="max-w-5xl mx-auto flex flex-wrap justify-end gap-3 mb-6">
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-sm transition">
            Export Excel
        </button>
        <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow-sm transition">
            Export PDF
        </button>
    </div>

    <!-- Rekap Konten -->
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Rekap Harian -->
        <div class="border border-purple-100 bg-white shadow-sm rounded-md p-5 hover:bg-gray-50 transition">
            <h2 class="text-lg font-bold text-purple-700 mb-2">Rekap Harian (01 Mei 2025)</h2>
            <p class="text-sm text-gray-700">Total Reservasi: <strong>5</strong></p>
            <p class="text-sm text-gray-700">Selesai: <strong>4</strong></p>
            <p class="text-sm text-gray-700">Dibatalkan: <strong>1</strong></p>
            <p class="text-sm text-gray-700">Total Pendapatan: <strong>Rp 850.000</strong></p>
        </div>

        <!-- Rekap Mingguan -->
        <div class="border border-purple-100 bg-white shadow-sm rounded-md p-5 hover:bg-gray-50 transition">
            <h2 class="text-lg font-bold text-purple-700 mb-2">Rekap Mingguan (28 Apr - 04 Mei 2025)</h2>
            <p class="text-sm text-gray-700">Total Reservasi: <strong>22</strong></p>
            <p class="text-sm text-gray-700">Selesai: <strong>18</strong></p>
            <p class="text-sm text-gray-700">Dibatalkan: <strong>4</strong></p>
            <p class="text-sm text-gray-700">Total Pendapatan: <strong>Rp 4.200.000</strong></p>
        </div>

        <!-- Rekap Bulanan -->
        <div class="border border-purple-100 bg-white shadow-sm rounded-md p-5 hover:bg-gray-50 transition">
            <h2 class="text-lg font-bold text-purple-700 mb-2">Rekap Bulanan (Mei 2025)</h2>
            <p class="text-sm text-gray-700">Total Reservasi: <strong>90</strong></p>
            <p class="text-sm text-gray-700">Selesai: <strong>80</strong></p>
            <p class="text-sm text-gray-700">Dibatalkan: <strong>10</strong></p>
            <p class="text-sm text-gray-700">Total Pendapatan: <strong>Rp 17.600.000</strong></p>
        </div>
    </div>
</div>
@endsection

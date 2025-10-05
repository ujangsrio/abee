<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-gray-700 py-4 mb-8 border-b-2 border-purple-200">
        Laporan Reservasi - Aretha Beauty
    </h1>

    <!-- Tombol Export -->
    <div class="max-w-5xl mx-auto flex flex-wrap justify-end gap-3 mb-6">
        <x-filament::button 
            icon="heroicon-o-table-cells"
            color="success"
            wire:click="$dispatch('exportExcel')"
            class="bg-green-600 hover:bg-green-700"
        >
            Export Excel
        </x-filament::button>
        
        <x-filament::button 
            icon="heroicon-o-document-arrow-down"
            color="danger"
            wire:click="$dispatch('exportPdf')"
            class="bg-red-600 hover:bg-red-700"
        >
            Export PDF
        </x-filament::button>
    </div>

    <!-- Rekap Konten -->
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Rekap Harian -->
        <div class="border border-purple-100 bg-white shadow-sm rounded-md p-5 hover:bg-gray-50 transition">
            <h2 class="text-lg font-bold text-purple-700 mb-2">Rekap Harian ({{ $rekapHarian['periode'] }})</h2>
            <div class="grid grid-cols-2 gap-2">
                <p class="text-sm text-gray-700">Total Reservasi: <strong>{{ $rekapHarian['total_reservasi'] }}</strong></p>
                <p class="text-sm text-gray-700">Selesai: <strong>{{ $rekapHarian['selesai'] }}</strong></p>
                <p class="text-sm text-gray-700">Dibatalkan: <strong>{{ $rekapHarian['dibatalkan'] }}</strong></p>
                <p class="text-sm text-gray-700">Total Pendapatan: <strong>Rp {{ number_format($rekapHarian['total_pendapatan'], 0, ',', '.') }}</strong></p>
            </div>
        </div>

        <!-- Rekap Mingguan -->
        <div class="border border-purple-100 bg-white shadow-sm rounded-md p-5 hover:bg-gray-50 transition">
            <h2 class="text-lg font-bold text-purple-700 mb-2">Rekap Mingguan ({{ $rekapMingguan['periode'] }})</h2>
            <div class="grid grid-cols-2 gap-2">
                <p class="text-sm text-gray-700">Total Reservasi: <strong>{{ $rekapMingguan['total_reservasi'] }}</strong></p>
                <p class="text-sm text-gray-700">Selesai: <strong>{{ $rekapMingguan['selesai'] }}</strong></p>
                <p class="text-sm text-gray-700">Dibatalkan: <strong>{{ $rekapMingguan['dibatalkan'] }}</strong></p>
                <p class="text-sm text-gray-700">Total Pendapatan: <strong>Rp {{ number_format($rekapMingguan['total_pendapatan'], 0, ',', '.') }}</strong></p>
            </div>
        </div>

        <!-- Rekap Bulanan -->
        <div class="border border-purple-100 bg-white shadow-sm rounded-md p-5 hover:bg-gray-50 transition">
            <h2 class="text-lg font-bold text-purple-700 mb-2">Rekap Bulanan ({{ $rekapBulanan['periode'] }})</h2>
            <div class="grid grid-cols-2 gap-2">
                <p class="text-sm text-gray-700">Total Reservasi: <strong>{{ $rekapBulanan['total_reservasi'] }}</strong></p>
                <p class="text-sm text-gray-700">Selesai: <strong>{{ $rekapBulanan['selesai'] }}</strong></p>
                <p class="text-sm text-gray-700">Dibatalkan: <strong>{{ $rekapBulanan['dibatalkan'] }}</strong></p>
                <p class="text-sm text-gray-700">Total Pendapatan: <strong>Rp {{ number_format($rekapBulanan['total_pendapatan'], 0, ',', '.') }}</strong></p>
            </div>
        </div>
    </div>

    <!-- Data Tabel Reservasi -->
    <div class="max-w-5xl mx-auto mt-8">
        <div class="border border-gray-200 rounded-lg overflow-hidden">
            {{ $this->table }}
        </div>
    </div>

    <!-- Script untuk handle export -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('exportExcel', () => {
                // Trigger export Excel
                console.log('Export Excel clicked');
            });
            
            @this.on('exportPdf', () => {
                // Trigger export PDF
                console.log('Export PDF clicked');
            });
        });
    </script>
</div>
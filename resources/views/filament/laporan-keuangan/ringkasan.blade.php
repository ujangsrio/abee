<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h3 class="font-semibold text-green-800 mb-2">💰 Pendapatan</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span>Dari Reservasi:</span>
                    <span class="font-semibold">Rp {{ number_format($ringkasan['pendapatan_reservasi'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Dari Manual:</span>
                    <span class="font-semibold">Rp {{ number_format($ringkasan['pendapatan_manual'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t pt-2 font-bold text-green-800">
                    <span>Total Pendapatan:</span>
                    <span>Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="font-semibold text-red-800 mb-2">📉 Pengeluaran</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between border-b pb-2 font-bold text-red-800">
                    <span>Total Pengeluaran:</span>
                    <span>Rp {{ number_format($ringkasan['total_pengeluaran'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-800 mb-2 text-center">📊 Laba Bersih</h3>
        <div class="text-center">
            <span class="text-2xl font-bold {{ $ringkasan['laba_bersih'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                Rp {{ number_format($ringkasan['laba_bersih'], 0, ',', '.') }}
            </span>
            <p class="text-sm text-gray-600 mt-1">
                {{ $ringkasan['laba_bersih'] >= 0 ? '✅ Keuntungan' : '❌ Kerugian' }}
            </p>
        </div>
    </div>

    @if($ringkasan['periode']['start'])
        <div class="text-xs text-gray-500 text-center">
            Periode: {{ \Carbon\Carbon::parse($ringkasan['periode']['start'])->format('d M Y') }} - 
            {{ \Carbon\Carbon::parse($ringkasan['periode']['end'])->format('d M Y') }}
        </div>
    @endif
</div>
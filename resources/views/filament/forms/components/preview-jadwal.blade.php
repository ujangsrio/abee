<div class="preview-jadwal bg-gray-50 p-4 rounded-lg border">
    <h4 class="text-sm font-medium text-gray-700 mb-3">Preview Jadwal Berdasarkan Konfigurasi</h4>
    
    <div class="grid grid-cols-7 gap-2 mb-4">
        @php
            $hariMap = [
                'senin' => ['label' => 'Sen', 'full' => 'Senin'],
                'selasa' => ['label' => 'Sel', 'full' => 'Selasa'],
                'rabu' => ['label' => 'Rab', 'full' => 'Rabu'],
                'kamis' => ['label' => 'Kam', 'full' => 'Kamis'],
                'jumat' => ['label' => 'Jum', 'full' => 'Jumat'],
                'sabtu' => ['label' => 'Sab', 'full' => 'Sabtu'],
                'minggu' => ['label' => 'Min', 'full' => 'Minggu'],
            ];
            
            $state = $getState();
            $hariOperasional = $state['hari_operasional'] ?? [];
            $jamBuka = $state['jam_buka_default'] ?? '08:00';
            $jamTutup = $state['jam_tutup_default'] ?? '17:00';
        @endphp
        
        @foreach($hariMap as $key => $hari)
            <div class="text-center p-2 rounded border text-xs 
                {{ in_array($key, $hariOperasional) 
                    ? 'bg-green-100 border-green-300 text-green-800' 
                    : 'bg-gray-100 border-gray-300 text-gray-500' }}">
                <div class="font-medium">{{ $hari['label'] }}</div>
                <div class="text-xs mt-1">
                    @if(in_array($key, $hariOperasional))
                        {{ $jamBuka }} - {{ $jamTutup }}
                    @else
                        Tutup
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($state['periode_mulai'] && $state['periode_selesai'])
        @php
            $start = \Carbon\Carbon::parse($state['periode_mulai']);
            $end = \Carbon\Carbon::parse($state['periode_selesai']);
            $totalHari = $start->diffInDays($end) + 1;
        @endphp
        <div class="text-xs text-gray-600">
            <strong>Periode:</strong> 
            {{ $start->translatedFormat('d M Y') }} 
            - 
            {{ $end->translatedFormat('d M Y') }}
            ({{ $totalHari }} hari)
        </div>
    @else
        <div class="text-xs text-yellow-600">
            Silakan atur periode dan hari operasional untuk melihat preview jadwal
        </div>
    @endif
</div>

<style>
.preview-jadwal {
    font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
}
</style>
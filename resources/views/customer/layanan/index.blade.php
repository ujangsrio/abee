@extends('customer.layout')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    
    $user = Auth::guard('customer')->user();
    $isMember = $user && $user->customer && $user->customer->is_member;
@endphp

<style>
    .layanan-card {
        border-radius: 15px;
        box-shadow: 0 6px 14px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        background-color: white;
        border: 1px solid #e9d5ff;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .layanan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .layanan-card img {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .layanan-card .card-body {
        padding: 16px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .layanan-card h3 {
        font-size: 18px;
        font-weight: 600;
        color: #7e3af2;
        margin-bottom: 6px;
    }

    .layanan-card p {
        font-size: 14px;
        line-height: 1.5;
        color: #555;
        flex-grow: 1;
    }

    .badge-kategori {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .badge-tipe {
        display: inline-block;
        padding: 3px 6px;
        border-radius: 8px;
        font-size: 10px;
        margin-right: 4px;
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #666;
        margin: 4px 0;
    }

    .harga-container {
        margin-top: auto;
        padding-top: 10px;
    }

    .jadwal-operasional {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        margin-top: 10px;
    }

    .jadwal-title {
        font-size: 12px;
        font-weight: 600;
        color: #4b5563;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .hari-list {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 6px;
    }

    .hari-item {
        padding: 2px 6px;
        background: #7e3af2;
        color: white;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 500;
    }

    .jam-operasional {
        font-size: 11px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .periode-operasional {
        font-size: 10px;
        color: #9ca3af;
        margin-top: 4px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        margin-left: 8px;
    }

    .status-aktif {
        background: #dcfce7;
        color: #166534;
    }

    .status-nonaktif {
        background: #fecaca;
        color: #dc2626;
    }
</style>

<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Pilih Layanan - Aretha Beauty
    </h1>

    @if(session('success'))
        <div class="max-w-6xl mx-auto mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-6xl mx-auto mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($layanan as $item)
            @php
                // Pastikan hanya menampilkan layanan aktif
                if (!$item->is_active) continue;

                $hargaAsli = $item->harga;
                
                // Logika promo sesuai dengan LayananResource
                $adaPromo = $item->is_promo && $item->promo;
                $hargaPromo = $adaPromo ? ($hargaAsli - ($hargaAsli * $item->promo->diskon / 100)) : null;
                
                // Parse tipe layanan
                $tipeLayanan = $item->tipe_layanan;
                if (is_string($tipeLayanan)) {
                    try {
                        $decoded = json_decode($tipeLayanan, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $tipeLayanan = $decoded;
                        }
                    } catch (\Exception $e) {
                        $tipeLayanan = ['studio'];
                    }
                }
                
                if (empty($tipeLayanan)) {
                    $tipeLayanan = ['studio'];
                }
                
                if (!is_array($tipeLayanan)) {
                    $tipeLayanan = [$tipeLayanan];
                }

                // Parse jadwal operasional dari waktu_operasional
                $waktuOperasional = $item->waktu_operasional;
                $jadwalOperasional = [
                    'hari_operasional' => [],
                    'jam_buka_default' => '08:00',
                    'jam_tutup_default' => '17:00',
                    'periode_mulai' => null,
                    'periode_selesai' => null
                ];

                if ($waktuOperasional && is_array($waktuOperasional)) {
                    $jadwalOperasional = array_merge($jadwalOperasional, $waktuOperasional);
                } elseif (is_string($waktuOperasional)) {
                    try {
                        $decodedJadwal = json_decode($waktuOperasional, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $jadwalOperasional = array_merge($jadwalOperasional, $decodedJadwal);
                        }
                    } catch (\Exception $e) {
                        // Tetap gunakan default
                    }
                }

                // Map hari Indonesia lengkap dan singkat
                $hariMap = [
                    'senin' => ['full' => 'Senin', 'short' => 'Sen'],
                    'selasa' => ['full' => 'Selasa', 'short' => 'Sel'],
                    'rabu' => ['full' => 'Rabu', 'short' => 'Rab'],
                    'kamis' => ['full' => 'Kamis', 'short' => 'Kam'],
                    'jumat' => ['full' => 'Jumat', 'short' => 'Jum'],
                    'sabtu' => ['full' => 'Sabtu', 'short' => 'Sab'],
                    'minggu' => ['full' => 'Minggu', 'short' => 'Min']
                ];

                // Warna kategori
                $kategoriColors = [
                    'kecantikan' => 'bg-pink-100 text-pink-800',
                    'kuku' => 'bg-purple-100 text-purple-800',
                    'henna' => 'bg-orange-100 text-orange-800',
                    'bulu_mata' => 'bg-blue-100 text-blue-800',
                    'rambut' => 'bg-yellow-100 text-yellow-800',
                    'lainnya' => 'bg-gray-100 text-gray-800',
                ];
                
                $kategoriColor = $kategoriColors[$item->kategori] ?? 'bg-gray-100 text-gray-800';
            @endphp

            <div class="layanan-card">
                {{-- Gambar Layanan --}}
                @if($item->gambar && Storage::disk('public')->exists($item->gambar))
                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama }}" class="object-cover">
                @else
                    <div class="h-48 bg-gradient-to-r from-purple-100 to-pink-100 flex items-center justify-center">
                        <span class="text-4xl text-purple-400">
                            @switch($item->kategori)
                                @case('kecantikan') 💄 @break
                                @case('kuku') 💅 @break
                                @case('henna') 🎨 @break
                                @case('bulu_mata') 👁️ @break
                                @case('rambut') 💇 @break
                                @default('lainnya') ✨ @break
                            @endswitch
                        </span>
                    </div>
                @endif

                <div class="card-body space-y-2">
                    {{-- Header dengan Status --}}
                    <div class="flex justify-between items-start">
                        {{-- Kategori --}}
                        <div class="badge-kategori {{ $kategoriColor }}">
                            {{ ucfirst(str_replace('_', ' ', $item->kategori)) }}
                        </div>
                        
                        {{-- Status Aktif --}}
                        <span class="status-badge {{ $item->is_active ? 'status-aktif' : 'status-nonaktif' }}">
                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    {{-- Nama Layanan --}}
                    <h3>{{ $item->nama }}</h3>

                    {{-- Deskripsi --}}
                    <p class="text-sm text-gray-600">{{ Str::limit($item->deskripsi, 100) }}</p>

                    {{-- Tipe Layanan --}}
                    <div class="mt-2">
                        @foreach($tipeLayanan as $tipe)
                            <span class="badge-tipe">
                                @if($tipe === 'studio')
                                    🏢 Studio
                                @elseif($tipe === 'home_service')
                                    🏠 Home Service
                                @else
                                    {{ ucfirst(str_replace('_', ' ', $tipe)) }}
                                @endif
                            </span>
                        @endforeach
                    </div>

                    {{-- Jadwal Operasional --}}
                    <div class="jadwal-operasional">
                        <div class="jadwal-title">
                            <span>📅 Jadwal Operasional</span>
                        </div>
                        
                        {{-- Hari Operasional --}}
                        @if(!empty($jadwalOperasional['hari_operasional']))
                            <div class="hari-list">
                                @foreach($jadwalOperasional['hari_operasional'] as $hari)
                                    @if(isset($hariMap[$hari]))
                                        <span class="hari-item" title="{{ $hariMap[$hari]['full'] }}">
                                            {{ $hariMap[$hari]['short'] }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-xs text-gray-500 mb-2">Belum diatur</div>
                        @endif

                        {{-- Jam Operasional --}}
                        <div class="jam-operasional">
                            <span>🕐 {{ $jadwalOperasional['jam_buka_default'] }} - {{ $jadwalOperasional['jam_tutup_default'] }}</span>
                        </div>

                        {{-- Periode Operasional --}}
                        @if($jadwalOperasional['periode_mulai'] && $jadwalOperasional['periode_selesai'])
                            @php
                                $periodeMulai = \Carbon\Carbon::parse($jadwalOperasional['periode_mulai'])->format('d/m/Y');
                                $periodeSelesai = \Carbon\Carbon::parse($jadwalOperasional['periode_selesai'])->format('d/m/Y');
                            @endphp
                            <div class="periode-operasional">
                                Periode: {{ $periodeMulai }} - {{ $periodeSelesai }}
                            </div>
                        @endif
                    </div>

                    {{-- Informasi Tambahan --}}
                    <div class="space-y-1 mt-2">
                        <div class="info-row">
                            <span>⏱️ Durasi:</span>
                            <span>{{ $item->estimasi_durasi }} menit</span>
                        </div>
                        <div class="info-row">
                            <span>👥 Kapasitas:</span>
                            <span>{{ $item->kapasitas_per_slot }} orang/slot</span>
                        </div>
                    </div>

                    {{-- Harga --}}
                    <div class="harga-container">
                        @if($adaPromo)
                            <div class="text-center">
                                <div class="text-purple-700 font-bold text-base">
                                    Rp {{ number_format($hargaPromo, 0, ',', '.') }}
                                </div>
                                <div class="text-sm text-gray-400 line-through">
                                    Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-green-600 font-semibold mt-1">
                                    🔥 Diskon {{ $item->promo->diskon }}%
                                    @if($item->promo->nama_promo)
                                        - {{ $item->promo->nama_promo }}
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="text-gray-800 font-semibold text-base text-center">
                                Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>

                    {{-- Tombol Booking --}}
                    <a href="{{ route('customer.booking.create', ['service_id' => $item->id]) }}" 
   class="mt-3 bg-purple-600 hover:bg-purple-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium transition-colors block">
    📅 Booking Sekarang
</a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pesan jika tidak ada layanan --}}
    @if($layanan->count() === 0)
        <div class="max-w-6xl mx-auto text-center py-12">
            <div class="text-6xl mb-4">😔</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tidak ada layanan tersedia</h3>
            <p class="text-gray-500">Silakan hubungi admin untuk informasi lebih lanjut.</p>
        </div>
    @endif

    {{-- Informasi tentang jadwal operasional --}}
    <div class="max-w-6xl mx-auto mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-semibold text-blue-800 mb-2">ℹ️ Informasi Jadwal Operasional</h3>
        <div class="text-sm text-blue-700 space-y-1">
            <p>• Setiap layanan memiliki jadwal operasional yang berbeda-beda</p>
            <p>• Hari operasional ditampilkan dalam bentuk singkatan (Sen, Sel, Rab, dll)</p>
            <p>• Jam operasional menunjukkan waktu pelayanan tersedia</p>
            <p>• Periode operasional menunjukkan rentang waktu layanan aktif</p>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk mengecek ketersediaan layanan berdasarkan jadwal
    function checkLayananAvailability() {
        const cards = document.querySelectorAll('.layanan-card');
        const now = new Date();
        const currentDay = now.toLocaleDateString('id-ID', { weekday: 'long' }).toLowerCase();
        const currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false });
        
        // Map hari Indonesia ke Inggris untuk matching
        const dayMapping = {
            'senin': 'monday',
            'selasa': 'tuesday',
            'rabu': 'wednesday',
            'kamis': 'thursday',
            'jumat': 'friday',
            'sabtu': 'saturday',
            'minggu': 'sunday'
        };

        cards.forEach(card => {
            const hariItems = card.querySelectorAll('.hari-item');
            const jamText = card.querySelector('.jam-operasional span').textContent;
            const jamMatch = jamText.match(/(\d{2}:\d{2}) - (\d{2}:\d{2})/);
            
            if (hariItems.length > 0 && jamMatch) {
                const jamBuka = jamMatch[1];
                const jamTutup = jamMatch[2];
                
                // Cek apakah hari ini termasuk hari operasional
                let isHariOperasional = false;
                hariItems.forEach(hariItem => {
                    const hariTitle = hariItem.getAttribute('title').toLowerCase();
                    if (dayMapping[hariTitle] === currentDay) {
                        isHariOperasional = true;
                    }
                });

                // Cek apakah saat ini dalam jam operasional
                const isJamOperasional = currentTime >= jamBuka && currentTime <= jamTutup;
                
                if (!isHariOperasional || !isJamOperasional) {
                    card.style.opacity = '0.7';
                    card.style.filter = 'grayscale(0.3)';
                    
                    // Tambahkan badge "Tutup"
                    const statusBadge = card.querySelector('.status-badge');
                    if (statusBadge && statusBadge.textContent === 'Aktif') {
                        const tutupBadge = document.createElement('span');
                        tutupBadge.className = 'status-badge status-nonaktif';
                        tutupBadge.textContent = 'Tutup';
                        tutupBadge.style.marginLeft = '8px';
                        statusBadge.parentNode.appendChild(tutupBadge);
                    }
                }
            }
        });
    }

    // Jalankan pengecekan ketersediaan saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        checkLayananAvailability();
    });
</script>
@endsection
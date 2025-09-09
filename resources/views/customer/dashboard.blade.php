@extends('customer.layout')

@section('content')
<div class="px-6 py-8 bg-white min-h-screen">
    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Hai, ✨ <span class="text-purple-600">{{ Auth::user()->name }}</span></h2>
        <p class="text-gray-600">Selamat datang di <strong>Aretha Beauty</strong> 💖</p>
    </div>

    {{-- Membership & Total Reservasi --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 max-w-3xl">
        {{-- Membership --}}
        <a href="{{ route('customer.akun.index') }}" class="border border-gray-200 bg-white p-4 shadow-sm hover:bg-gray-50 transition">
            <h3 class="font-semibold text-base text-gray-700">👤 Membership</h3>
            @if ($membership)
                <p class="mt-1 text-sm text-gray-600">Kode Member: <strong>{{ $membership->member_code }}</strong></p>
            @else
                <p class="mt-1 text-sm text-gray-600">Belum terdaftar 😢<br><strong class="text-purple-600">Daftar yuk!</strong></p>
            @endif
        </a>

        {{-- Total Reservasi --}}
        <a href="{{ route('customer.history.index') }}" class="border border-gray-200 bg-white p-4 shadow-sm hover:bg-gray-50 transition">
            <h3 class="font-semibold text-base text-gray-700">📅 Total Reservasi</h3>
            <p class="mt-1 text-2xl font-bold text-gray-800">{{ $totalReservations }}</p>
        </a>
    </div>

    {{-- Reservasi Aktif --}}
    <a href="{{ route('customer.reservasiaktif') }}" class="block bg-white border border-purple-200 p-4 mb-6 shadow hover:bg-purple-50 transition">
        <h3 class="text-lg font-bold text-purple-800 mb-2">🕒 Reservasi Aktif</h3>
        @if ($reservasiAktif->isNotEmpty())
            @foreach ($reservasiAktif as $booking)
                <ul class="text-sm mb-3">
                    <li><strong>Layanan:</strong> {{ $booking->service->nama ?? '-' }}</li>
                    <li><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</li>
                    <li><strong>Status:</strong> <span class="font-semibold text-green-600">{{ ucfirst($booking->status) }}</span></li>
                </ul>
                <hr class="my-2 border-gray-300">
            @endforeach
        @else
            <p class="text-gray-500 text-sm">Tidak ada reservasi aktif saat ini.</p>
        @endif
    </a>

    {{-- Promo Mingguan --}}
    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 text-gray-800 mb-6">
        🎁 <strong>Promo Minggu Ini:</strong>
        @if ($promoLayanan)
            <span class="italic">Diskon {{ $promoLayanan->diskon }}%</span> untuk <strong>{{ $promoLayanan->nama_promo }}</strong>!
        @else
            Belum ada promo minggu ini.
        @endif
    </div>

    {{-- Semua Promo --}}
    @if($semuaPromo->count())
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-3">🔥 Promo Tersedia</h3>
            <ul class="space-y-3">
                @foreach ($semuaPromo as $promo)
                    <li class="bg-white border border-purple-200 p-4 shadow hover:bg-purple-50 transition text-gray-800 cursor-pointer"
                        onclick="window.location.href='{{ route('customer.layanan') }}'">
                        <div class="flex justify-between items-center mb-1 font-semibold">
                            🎁 {{ $promo->nama_promo }}
                            @if ($promo->hanya_member)
                                <span class="bg-purple-600 text-white text-xs px-2 py-1">Member Only</span>
                            @endif
                        </div>
                        <div class="text-sm">{{ $promo->deskripsi }}</div>
                        <div class="mt-1 text-sm">
                            Diskon: <strong>{{ $promo->diskon }}%</strong><br>
                            Berlaku sampai: {{ \Carbon\Carbon::parse($promo->tanggal_berakhir)->translatedFormat('d M Y') }}
                        </div>
                        @if($promo->hanya_member && !$membership)
                            <div class="mt-2 text-xs text-red-600">❗ Promo ini hanya untuk member. Yuk daftar dulu!</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Tombol --}}
    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('customer.layanan') }}" class="bg-purple-500 hover:bg-purple-600 text-white font-bold px-4 py-2 transition">+ Buat Reservasi Baru</a>
        <a href="{{ route('customer.history.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold px-4 py-2 transition">Lihat Riwayat Reservasi</a>
    </div>

    {{-- Daftar Layanan --}}
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-3">💆‍♀️ Daftar Layanan Kami</h3>
        @if ($layanan->isNotEmpty())
            <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($layanan as $item)
                    <li onclick="window.location.href='{{ route('customer.layanan') }}'" class="bg-white border border-gray-200 shadow p-4 hover:bg-gray-50 transition cursor-pointer">
                        <div class="font-semibold">{{ $item->nama }}</div>
                        <div class="text-sm text-gray-500">Harga: Rp{{ number_format($item->harga, 0, ',', '.') }}</div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Belum ada layanan yang tersedia.</p>
        @endif
    </div>
</div>
@endsection

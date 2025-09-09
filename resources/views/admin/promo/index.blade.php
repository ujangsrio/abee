@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Manajemen Promo - Aretha Beauty
    </h1>

    {{-- Tombol Tambah Promo --}}
    <div class="max-w-4xl mx-auto mb-6 text-right">
        <a href="{{ route('admin.promo.create') }}" class="inline-block bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded shadow-sm">
            + Tambah Promo Baru
        </a>
    </div>

    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="max-w-4xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Daftar Promo --}}
    <div class="max-w-4xl mx-auto space-y-4 mb-8">
        @forelse($promos as $promo)
        <div class="border border-purple-100 bg-white p-5 rounded-md shadow-sm hover:bg-gray-50 transition">
            <h2 class="text-lg font-bold text-purple-800">{{ $promo->nama_promo }}</h2>
            <p class="text-sm text-gray-700 mb-1">{{ $promo->deskripsi }}</p>
            <p class="text-sm text-gray-700">Diskon: <strong>{{ $promo->diskon }}%</strong></p>
            
            @if($promo->hanya_member)
                <p class="inline-block text-xs font-medium mt-1 px-2 py-1 bg-yellow-200 text-yellow-800 rounded-sm">Khusus Member</p>
            @endif

            <p class="text-sm text-gray-500 mt-2">
                Berakhir: {{ \Carbon\Carbon::parse($promo->tanggal_berakhir)->translatedFormat('d F Y') }}
            </p>

            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('admin.promo.show', $promo->id) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm">Lihat Detail</a>
                <a href="{{ route('admin.promo.edit', $promo->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Edit</a>
                <form action="{{ route('admin.promo.destroy', $promo->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus promo ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center italic">Belum ada promo yang tersedia.</p>
        @endforelse
    </div>
</div>
@endsection

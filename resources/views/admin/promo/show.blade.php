@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Detail Promo - {{ $promo->nama_promo }}
    </h1>

    <div class="max-w-5xl mx-auto bg-white shadow-md border border-purple-100 rounded-sm p-6 space-y-6">
        {{-- Nama Promo --}}
        <div>
            <h2 class="text-sm font-medium text-gray-500 mb-1">Nama Promo</h2>
            <p class="text-lg font-semibold text-gray-800">{{ $promo->nama_promo }}</p>
        </div>

        {{-- Deskripsi --}}
        <div>
            <h2 class="text-sm font-medium text-gray-500 mb-1">Deskripsi</h2>
            <p class="text-gray-800">{{ $promo->deskripsi }}</p>
        </div>

        {{-- Diskon --}}
        <div>
            <h2 class="text-sm font-medium text-gray-500 mb-1">Diskon</h2>
            <p class="text-gray-800 font-semibold">{{ $promo->diskon }}%</p>
        </div>

        {{-- Khusus Member --}}
        @if ($promo->hanya_member)
            <div>
                <span class="inline-block bg-yellow-300 text-black text-xs font-medium px-3 py-1 rounded-full">
                    Promo khusus untuk member
                </span>
            </div>
        @endif

        {{-- Tanggal Berakhir --}}
        <div>
            <h2 class="text-sm font-medium text-gray-500 mb-1">Tanggal Berakhir</h2>
            <p class="text-gray-800">
                {{ \Carbon\Carbon::parse($promo->tanggal_berakhir)->translatedFormat('d F Y') }}
            </p>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex justify-between pt-6">
            <a href="{{ route('admin.promo.index') }}" class="text-purple-700 hover:underline text-sm">
                ← Kembali ke daftar promo
            </a>
            <a href="{{ route('admin.promo.edit', $promo->id) }}" class="bg-purple-500 hover:bg-purple-600 text-white px-5 py-2 rounded-md text-sm">
                Edit Promo
            </a>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="p-6 max-w-6xl mx-auto bg-white min-h-screen">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Tambah Promo Baru
    </h1>

    {{-- Error Validasi --}}
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-700 rounded-md shadow-sm">
            <ul class="list-disc pl-5 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-6 border border-purple-100 rounded-md shadow-sm max-w-3xl mx-auto">
        <form action="{{ route('admin.promo.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Nama Promo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Promo</label>
                <input type="text" name="nama_promo" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Masukkan nama promo" value="{{ old('nama_promo') }}" required>
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Masukkan deskripsi promo" required>{{ old('deskripsi') }}</textarea>
            </div>

            {{-- Diskon --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diskon (%)</label>
                <input type="number" name="diskon" min="1" max="100" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" placeholder="Contoh: 15" value="{{ old('diskon') }}" required>
            </div>

            {{-- Hanya Member --}}
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="hanya_member" value="1" class="mr-2" {{ old('hanya_member') ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Promo khusus untuk member</span>
                </label>
            </div>

            {{-- Tanggal Berakhir --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berakhir</label>
                <input type="date" name="tanggal_berakhir" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('tanggal_berakhir') }}" required>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('admin.promo.index') }}" class="text-purple-700 hover:underline text-sm">
                    ← Kembali ke daftar promo
                </a>
                <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-5 py-2 rounded-md">
                    Simpan Promo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

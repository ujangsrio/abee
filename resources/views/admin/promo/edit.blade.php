@extends('layouts.app')

@section('content')
<div class="p-6 max-w-6xl mx-auto bg-white min-h-screen">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Edit Promo - {{ $promo->nama_promo }}
    </h1>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-700 rounded-md shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white p-6 border border-purple-100 rounded-md shadow-sm max-w-3xl mx-auto">
        <form action="{{ route('admin.promo.update', $promo->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Nama Promo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Promo</label>
                <input type="text" name="nama_promo" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('nama_promo', $promo->nama_promo) }}" required>
                @error('nama_promo')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" required>{{ old('deskripsi', $promo->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Diskon --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Diskon (%)</label>
                <input type="number" name="diskon" min="1" max="100" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('diskon', $promo->diskon) }}" required>
                @error('diskon')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hanya Member --}}
            <div class="mt-2">
                <label class="inline-flex items-center">
                    <input type="hidden" name="hanya_member" value="0">
                    <input type="checkbox" name="hanya_member" value="1" class="mr-2" {{ old('hanya_member', $promo->hanya_member) ? 'checked' : '' }}>
                    <span class="text-sm text-gray-700">Promo khusus untuk member</span>
                </label>
            </div>

            {{-- Tanggal Berakhir --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berakhir</label>
                <input type="date" name="tanggal_berakhir" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('tanggal_berakhir', $promo->tanggal_berakhir) }}" required>
                @error('tanggal_berakhir')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex justify-between items-center pt-4">
                <a href="{{ route('admin.promo.index') }}" class="text-purple-700 hover:underline text-sm">
                    ← Kembali ke daftar promo
                </a>
                <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-5 py-2 rounded-md">
                    Update Promo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

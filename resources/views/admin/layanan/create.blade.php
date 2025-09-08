@extends('layouts.app')

@section('content')
<div class="p-6 max-w-6xl mx-auto bg-white min-h-screen">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Tambah Layanan Baru
    </h1>

    <form action="{{ route('admin.layanan.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 bg-white p-6 border border-purple-100 rounded-none shadow-md">
        @csrf

        {{-- Nama Layanan --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Nama Layanan</label>
            <input type="text" name="nama" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('nama') }}" required>
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" required>{{ old('deskripsi') }}</textarea>
        </div>

        {{-- Harga --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Harga</label>
            <input type="number" name="harga" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('harga') }}" required>
        </div>

        {{-- Tanggal --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Tanggal</label>
            <input type="date" name="tanggal" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('tanggal') }}" required>
        </div>

        {{-- Slot Jam --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Slot Jam (bisa lebih dari satu)</label>
            <div id="slot-container" class="space-y-2">
                <div class="flex space-x-2">
                    <input type="time" name="slots[]" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                    <button type="button" onclick="addSlot()" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md text-sm">+</button>
                </div>
            </div>
        </div>

        {{-- Gambar --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Gambar</label>
            <input type="file" name="gambar" class="w-full border border-gray-300 rounded-md px-3 py-2">
        </div>

        {{-- Promo --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Pilih Promo (Opsional)</label>
            <select name="promo_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300">
                <option value="">-- Tidak ada promo --</option>
                @foreach($promos as $promo)
                    <option value="{{ $promo->id }}" {{ old('promo_id') == $promo->id ? 'selected' : '' }}>
                        {{ $promo->nama_promo }} - {{ $promo->diskon }}% @if($promo->hanya_member) (Member) @endif
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-between items-center pt-4">
            <a href="{{ route('admin.layanan.index') }}" class="text-purple-700 hover:underline text-sm">
                ← Kembali
            </a>
            <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-5 py-2 rounded-md">
                Simpan
            </button>
        </div>
    </form>
</div>

{{-- Tambah slot dinamis --}}
<script>
    function addSlot() {
        const container = document.getElementById('slot-container');
        const div = document.createElement('div');
        div.classList.add('flex', 'space-x-2', 'items-center');

        div.innerHTML = `
            <input type="time" name="slots[]" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
            <button type="button" onclick="this.parentElement.remove()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm">-</button>
        `;

        container.appendChild(div);
    }
</script>
@endsection

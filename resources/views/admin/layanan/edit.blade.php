@extends('layouts.app')

@section('content')
<div class="p-6 max-w-6xl mx-auto bg-white min-h-screen">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Edit Layanan - {{ $layanan->nama }}
    </h1>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 p-4 rounded-md border border-red-300 mb-6">
            <strong>Terjadi kesalahan:</strong>
            <ul class="list-disc pl-5 mt-2 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.layanan.update', $layanan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5 bg-white p-6 border border-purple-100 rounded-none shadow-md">
        @csrf
        @method('PUT')

        {{-- Nama --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Nama Layanan</label>
            <input type="text" name="nama" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('nama', $layanan->nama) }}" required>
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" required>{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
        </div>

        {{-- Harga --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Harga</label>
            <input type="number" name="harga" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('harga', $layanan->harga) }}" required>
        </div>

        {{-- Tanggal --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Tanggal</label>
            <input type="date" name="tanggal" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ old('tanggal', $layanan->tanggal) }}" required>
        </div>

        {{-- Slot Jam --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Slot Jam (bisa lebih dari satu)</label>
            <div id="slot-container" class="space-y-2">
                @foreach ($layanan->slots as $slot)
                    <div class="flex gap-2 items-center">
                        <input type="hidden" name="slot_ids[]" value="{{ $slot->id }}">
                        <input type="time" name="slots[]" step="60" required class="border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-purple-300" value="{{ $slot->jam }}">
                        <button type="button" onclick="this.parentElement.remove()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm">Hapus</button>
                    </div>
                @endforeach
            </div>
            <button type="button" onclick="tambahSlot()" class="mt-3 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                + Tambah Slot
            </button>
        </div>

        {{-- Gambar --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Gambar Baru (Opsional)</label>
            <input type="file" name="gambar" class="w-full border border-gray-300 rounded-md px-3 py-2">
            @if ($layanan->gambar)
                <p class="text-sm text-gray-600 mt-2">Gambar saat ini:</p>
                <img src="{{ asset('storage/photos/' . $layanan->gambar) }}" class="w-20 mt-1 rounded-md shadow-sm border">
            @endif
        </div>

        {{-- Promo --}}
        <div>
            <label class="block mb-1 font-medium text-gray-700">Pilih Promo (Opsional)</label>
            <select name="promo_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300">
                <option value="">-- Tidak ada promo --</option>
                @foreach($promos as $promo)
                    <option value="{{ $promo->id }}" {{ old('promo_id', $layanan->promo_id) == $promo->id ? 'selected' : '' }}>
                        {{ $promo->nama_promo }} - {{ $promo->diskon }}%
                        @if($promo->hanya_member) (Member) @endif
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
                Update
            </button>
        </div>
    </form>
</div>

{{-- Script slot --}}
<script>
    function tambahSlot() {
        const container = document.getElementById('slot-container');
        const div = document.createElement('div');
        div.classList.add('flex', 'gap-2', 'items-center');
        div.innerHTML = `
            <input type="hidden" name="slot_ids[]" value="">
            <input type="time" name="slots[]" step="60" class="border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-purple-300" required>
            <button type="button" onclick="this.parentElement.remove()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm">Hapus</button>
        `;
        container.appendChild(div);
    }
</script>
@endsection

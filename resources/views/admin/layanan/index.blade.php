@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Manajemen Layanan - Aretha Beauty
    </h1>

    <div class="max-w-6xl mx-auto bg-white shadow-md rounded-none p-6 border border-purple-100">
        <a href="{{ route('admin.layanan.create') }}" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded text-sm font-medium mb-6 inline-block">
            + Tambah Layanan
        </a>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-300 rounded-md overflow-hidden">
                <thead class="bg-purple-100 text-black-700 font-medium">
                    <tr>
                        <th class="py-3 px-4 border">ID</th>
                        <th class="py-3 px-4 border">Nama</th>
                        <th class="py-3 px-4 border">Gambar</th>
                        <th class="py-3 px-4 border">Harga</th>
                        <th class="py-3 px-4 border">Promo</th>
                        <th class="py-3 px-4 border">Slot Tersedia</th>
                        <th class="py-3 px-4 border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($layanans as $layanan)
                        <tr class="hover:bg-purple-50 transition">
                            <td class="py-3 px-4 border text-gray-800">{{ $layanan->id }}</td>
                            <td class="py-3 px-4 border text-gray-800">{{ $layanan->nama }}</td>
                            <td class="py-3 px-4 border">
                                @if($layanan->gambar)
                                    <img src="{{ asset('storage/photos/' . $layanan->gambar) }}" alt="gambar" class="w-16 h-16 object-cover rounded-md shadow-sm">
                                @else
                                    <span class="text-sm text-gray-500 italic">Tidak ada</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border text-gray-800">Rp {{ number_format($layanan->harga, 0, ',', '.') }}</td>

                            {{-- Kolom Promo --}}
                            <td class="py-3 px-4 border">
                                @if($layanan->promo)
                                    <div class="space-y-1">
                                        <span class="font-semibold text-purple-700">{{ $layanan->promo->nama_promo }}</span>
                                        <div class="text-sm text-gray-700">
                                            Diskon {{ $layanan->promo->diskon }}%
                                            @if($layanan->promo->hanya_member)
                                                <span class="ml-2 text-xs bg-yellow-300 text-black px-2 py-1 rounded-sm">Khusus Member</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500 italic">Tidak ada</span>
                                @endif
                            </td>

                            {{-- Kolom Slot --}}
                            <td class="py-3 px-4 border">
                                @if($layanan->slots->count())
                                    <ul class="text-sm text-gray-800 space-y-1">
                                        @foreach($layanan->slots as $slot)
                                            <li>
                                                {{ \Carbon\Carbon::parse($slot->tanggal)->format('d/m/Y') }} -
                                                {{ \Carbon\Carbon::parse($slot->jam)->format('H:i') }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-sm text-gray-500 italic">Tidak ada slot</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3 px-4 border whitespace-nowrap">
                                <a href="{{ route('admin.layanan.edit', $layanan->id) }}" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs font-medium mr-2">
                                    Edit
                                </a>
                                <form action="{{ route('admin.layanan.destroy', $layanan->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin hapus layanan ini?')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

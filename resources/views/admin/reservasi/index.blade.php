@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Manajemen Reservasi - Aretha Beauty
    </h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-6xl mx-auto bg-white shadow-md rounded-none p-6 border border-purple-100">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-300 rounded-md overflow-hidden">
                <thead class="bg-purple-100 text-black-700 font-medium">
                    <tr>
                        <th class="py-3 px-4 border">No</th>
                        <th class="py-3 px-4 border">Nama Pelanggan</th>
                        <th class="py-3 px-4 border">Kontak</th>
                        <th class="py-3 px-4 border">Layanan</th>
                        <th class="py-3 px-4 border">Jadwal</th>
                        <th class="py-3 px-4 border">Bukti Transfer</th>
                        <th class="py-3 px-4 border">Status DP</th>
                        <th class="py-3 px-4 border">Status Reservasi</th>
                        <th class="py-3 px-4 border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($bookings as $index => $r)
                        <tr class="hover:bg-purple-50 transition">
                            <td class="py-3 px-4 border text-gray-800">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 border text-gray-800 font-medium">{{ $r->customer_name }}</td>
                            <td class="py-3 px-4 border text-gray-700">{{ $r->customer->whatsapp ?? '-' }}</td>
                            <td class="py-3 px-4 border text-gray-700">{{ $r->service->nama ?? '-' }}</td>
                            <td class="py-3 px-4 border text-gray-700">
                                {{ \Carbon\Carbon::parse($r->date . ' ' . $r->time)->translatedFormat('l, d F Y H:i') }}
                            </td>
                            <td class="py-3 px-4 border">
                                @if ($r->bukti_transfer)
                                    <a href="{{ asset('storage/' . $r->bukti_transfer) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $r->bukti_transfer) }}" alt="Bukti Transfer" class="w-20 h-20 object-cover rounded shadow">
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border">
                                <div class="flex flex-col space-y-2">
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        {{ $r->status_dp === 'Belum' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $r->status_dp === 'Belum' ? 'Belum DP' : 'DP Lunas' }}
                                    </span>
                                    @if($r->status_dp === 'Belum' && $r->bukti_transfer)
                                        <div class="flex space-x-1">
                                            <form action="{{ route('admin.reservasi.confirmDp', $r->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                                                     Konfirmasi
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.reservasi.rejectDp', $r->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                                     Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 border">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $r->status === 'Menunggu' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($r->status === 'Dikonfirmasi' ? 'bg-green-100 text-green-800' : 
                                       ($r->status === 'Dibatalkan' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                    {{ ucfirst($r->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border">
                                <form action="{{ route('admin.reservasi.updateStatus', $r->id) }}" method="POST" class="flex items-center space-x-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="text-sm border border-gray-300 rounded px-2 py-1">
                                        <option value="Menunggu" {{ $r->status == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="Dikonfirmasi" {{ $r->status == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                                        <option value="Dibatalkan" {{ $r->status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                        <option value="Selesai" {{ $r->status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-500 italic">Tidak ada reservasi ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

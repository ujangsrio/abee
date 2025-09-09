@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h2 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Histori Layanan - Aretha Beauty
    </h2>

    {{-- Filter Status --}}
    <form method="GET" action="{{ route('admin.histori.index') }}" class="mb-6 flex justify-center">
        <select name="status" onchange="this.form.submit()" 
            class="border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
            <option value="">-- Semua Status --</option>
            <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
            <option value="Dikonfirmasi" {{ request('status') == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
            <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
        </select>
    </form>

    {{-- Hasil --}}
    @if($histories->isEmpty())
        <div class="text-center text-gray-500 italic">Tidak ada histori layanan.</div>
    @else
        <div class="max-w-5xl mx-auto space-y-4">
            @foreach ($histories as $item)
                <div class="border border-purple-100 rounded-md bg-white p-4 hover:bg-gray-50 transition">
                    <h3 class="text-lg font-semibold text-purple-800 mb-1">
                        {{ $item->service->nama ?? 'Layanan Tidak Ditemukan' }}
                    </h3>
                    <p class="text-sm text-gray-700">
                        <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d F Y') }}
                    </p>
                    <p class="text-sm text-gray-700">
                        <strong>Waktu:</strong> {{ $item->time ?? '-' }}
                    </p>
                    <p class="text-sm">
                        <strong>Status:</strong> 
                        <span class="inline-block px-2 py-1 rounded 
                            {{ 
                                $item->status === 'Selesai' ? 'bg-green-200 text-green-800' :
                                ($item->status === 'Dibatalkan' ? 'bg-red-200 text-red-800' :
                                ($item->status === 'Dikonfirmasi' ? 'bg-blue-200 text-blue-800' : 'bg-yellow-200 text-yellow-800'))
                            }}">
                            {{ $item->status }}
                        </span>
                    </p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

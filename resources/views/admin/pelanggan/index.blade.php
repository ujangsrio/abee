@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Manajemen Pelanggan - Aretha Beauty
    </h1>

    {{-- Keterangan --}}
    <div class="max-w-5xl mx-auto bg-white shadow-md rounded-none p-6 border border-purple-100">
        <h2 class="text-xl font-semibold text-black-700 mb-6">
            Semua Pelanggan Terdaftar
        </h2>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-300 rounded-md overflow-hidden">
                <thead class="bg-purple-100 text-black-700 font-medium">
                    <tr>
                        <th class="px-4 py-3 border">No.</th>
                        <th class="px-4 py-3 border">Nama</th>
                        <th class="px-4 py-3 border">Email</th>
                        <th class="px-4 py-3 border">No. WhatsApp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pelanggan as $index => $p)
                        <tr class="hover:bg-purple-50 transition">
                            <td class="px-4 py-3 border text-gray-800">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 border font-medium text-gray-800">{{ $p->name }}</td>
                            <td class="px-4 py-3 border text-gray-700">{{ $p->user->email ?? '-' }}</td>
                            <td class="px-4 py-3 border text-gray-700">{{ $p->whatsapp ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 italic">Belum ada pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

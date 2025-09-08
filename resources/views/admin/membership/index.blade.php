@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-black-200">
        Manajemen Membership - Aretha Beauty
    </h1>

    {{-- Pelanggan dari Form Membership --}}
    <div class="max-w-5xl mx-auto bg-white shadow-md rounded-none p-6 border border-purple-100">
        <h2 class="text-xl font-semibold text-black-700 mb-6">
            Pelanggan yang Terdaftar Membership
        </h2>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-300 rounded-md overflow-hidden">
                <thead class="bg-purple-100 text-black-700 font-medium">
                    <tr>
                        <th class="py-3 px-4 border">No.</th>
                        <th class="py-3 px-4 border">Nama</th>
                        <th class="py-3 px-4 border">No. WhatsApp</th>
                        <th class="py-3 px-4 border">Kode Membership</th>
                        <th class="py-3 px-4 border">Tanggal Daftar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($customermemberships as $index => $member)
                        <tr class="hover:bg-purple-50 transition">
                            <td class="py-3 px-4 border text-gray-800">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 border font-medium text-gray-800">{{ $member->name }}</td>
                            <td class="py-3 px-4 border text-gray-700">{{ $member->whatsapp }}</td>
                            <td class="py-3 px-4 border">
                                <span class="text-black-800 text-xs font-semibold px-3 py-1">
                                    {{ $member->member_code }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border text-gray-700">{{ $member->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6 text-gray-500 italic">
                                Belum ada pelanggan dari form membership.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

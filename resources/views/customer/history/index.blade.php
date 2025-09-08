@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        History Reservasi - Aretha Beauty
    </h1>


    @if (session('success'))
        <div class="mb-6 max-w-4xl mx-auto p-4 bg-green-100 text-green-800 border border-green-300 rounded-md shadow-sm animate-fade-in text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if ($bookings->isEmpty())
        <div class="max-w-4xl mx-auto text-center text-gray-500 italic mt-10 flex flex-col items-center animate-fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-7 4h8a2 2 0 002-2v-6a2 2 0 00-2-2h-8a2 2 0 00-2 2v6a2 2 0 002 2z" />
            </svg>
            Tidak ada history reservasi.
        </div>
    @else
        <div class="max-w-4xl mx-auto bg-white border border-purple-100 rounded-sm shadow-sm overflow-x-auto animate-fade-in">
            <table class="min-w-full bg-white rounded-md text-sm text-gray-800">
                <thead class="bg-purple-100 text-purple-800 uppercase font-semibold tracking-wide">
                    <tr>
                        <th class="py-3 px-4 text-left">Layanan</th>
                        <th class="py-3 px-4 text-left">Tanggal</th>
                        <th class="py-3 px-4 text-left">Waktu</th>
                        <th class="py-3 px-4 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($bookings as $index => $booking)
                        <tr class="hover:bg-purple-50 transition animate-fade-in-down delay-{{ $index * 50 }}">
                            <td class="py-3 px-4 font-medium">{{ $booking->service->nama ?? '-' }}</td>
                            <td class="py-3 px-4">{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>
                            <td class="py-3 px-4">{{ $booking->time }}</td>
                            <td class="py-3 px-4 flex items-center gap-2">
                                @php
                                    $status = strtolower($booking->status);
                                    $statusClass = 'text-gray-600 font-semibold';
                                    $icon = '<svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5" /></svg>';

                                    if ($status == 'selesai') {
                                        $statusClass = 'text-purple-700 font-bold';
                                        $icon = '<svg class="h-5 w-5 inline-block text-purple-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>';
                                    } elseif ($status == 'dibatalkan') {
                                        $statusClass = 'text-red-600 font-bold';
                                        $icon = '<svg class="h-5 w-5 inline-block text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';
                                    } elseif ($status == 'menunggu') {
                                        $statusClass = 'text-yellow-600 font-semibold italic';
                                        $icon = '<svg class="h-5 w-5 inline-block text-yellow-500" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 2" /><circle cx="12" cy="12" r="10" /></svg>';
                                    } elseif ($status == 'diterima') {
                                        $statusClass = 'text-purple-700 font-semibold';
                                        $icon = '<svg class="h-5 w-5 inline-block text-purple-600" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>';
                                    }
                                @endphp
                                {!! $icon !!}
                                <span class="{{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- Animasi --}}
<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes fade-in-down {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.5s ease-out forwards; }
    .animate-fade-in-down { animation: fade-in-down 0.5s ease-out forwards; }
</style>
@endsection

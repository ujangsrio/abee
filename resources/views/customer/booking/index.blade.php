@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Daftar Booking Anda
    </h1>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($bookings->count())
        <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-800">
                <thead class="bg-purple-100 text-purple-800 text-sm font-semibold border-b">
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Layanan</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Variasi</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Dibuat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($bookings as $booking)
                        <tr class="hover:bg-purple-50 transition">
                            <td class="px-4 py-2">{{ $booking->id }}</td>
                            <td class="px-4 py-2">{{ $booking->customer_name }}</td>
                            <td class="px-4 py-2">{{ $booking->service->nama ?? '-' }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->date)->format('d-m-Y') }}</td>
                            <td class="px-4 py-2">{{ $booking->time }}</td>
                            <td class="px-4 py-2">
                                @if($booking->variasi)
                                    <ul class="list-disc list-inside text-gray-600 text-xs space-y-1">
                                        @foreach(json_decode($booking->variasi) as $var)
                                            <li>{{ $var }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @php
                                    $status = strtolower($booking->status ?? 'pending');
                                    $color = match($status) {
                                        'selesai' => 'bg-green-100 text-green-800',
                                        'batal' => 'bg-red-100 text-red-700',
                                        'diproses' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $color }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500">
                                {{ $booking->created_at->format('d-m-Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="max-w-4xl mx-auto bg-white border border-gray-200 p-6 rounded-sm shadow-sm text-center text-gray-600">
            Anda belum memiliki booking.
        </div>
    @endif
</div>
@endsection

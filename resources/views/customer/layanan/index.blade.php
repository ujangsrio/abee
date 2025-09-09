@extends('customer.layout')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::guard('customer')->user();
    $isMember = $user && $user->customer && $user->customer->is_member;
@endphp

<style>
    .layanan-card {
        border-radius: 15px;
        box-shadow: 0 6px 14px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        background-color: white;
        border: 1px solid #e9d5ff;
    }

    .layanan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .layanan-card img {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .layanan-card .card-body {
        padding: 16px;
        text-align: center;
    }

    .layanan-card h3 {
        font-size: 18px;
        font-weight: 600;
        color: #7e3af2;
        margin-bottom: 6px;
    }

    .layanan-card p {
        font-size: 14px;
        line-height: 1.5;
        color: #555;
    }
</style>

<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Pilih Layanan - Aretha Beauty
    </h1>

    <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($layanan as $item)
            @php
                $hargaAsli = $item->harga;
                $adaPromo = $item->promo && ($item->promo->hanya_member ? $isMember : true);
                $hargaPromo = $adaPromo ? ($hargaAsli - ($hargaAsli * $item->promo->diskon / 100)) : null;
            @endphp

            <div class="layanan-card">
                <img src="{{ asset('storage/photos/' . $item->gambar) }}" alt="{{ $item->nama }}">

                <div class="card-body space-y-2">
                    {{-- Nama Layanan --}}
                    <h3>{{ $item->nama }}</h3>

                    {{-- Deskripsi --}}
                    <p class="text-sm text-gray-600">{{ $item->deskripsi }}</p>

                    {{-- Harga --}}
                    @if($adaPromo)
                        <div class="text-center">
                            <div class="text-purple-700 font-bold text-base">
                                Rp {{ number_format($hargaPromo, 0, ',', '.') }}
                            </div>
                            <div class="text-sm text-gray-400 line-through">
                                Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                            </div>
                        </div>
                    @else
                        <div class="text-gray-800 font-semibold text-base">Rp {{ number_format($hargaAsli, 0, ',', '.') }}</div>
                    @endif

                    {{-- Promo --}}
                    @if($item->promo)
                        @if($item->promo->hanya_member)
                            <div class="inline-block text-[12px] font-medium px-2 py-0.5 rounded
                                {{ $isMember ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $isMember ? 'Diskon Member ' . $item->promo->diskon . '%' : 'Promo Khusus Member' }}
                            </div>
                        @else
                            <div class="inline-block text-[12px] bg-purple-100 text-purple-800 font-medium px-2 py-0.5 rounded">
                                Diskon {{ $item->promo->diskon }}%
                            </div>
                        @endif
                    @else
                        <div class="inline-block text-[12px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded">
                            Tidak ada promo
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-10 text-center">
        <a href="{{ route('customer.booking.create') }}"
           class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md font-semibold transition">
            Booking Sekarang
        </a>
    </div>
</div>
@endsection

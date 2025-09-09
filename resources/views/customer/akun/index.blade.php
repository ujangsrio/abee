@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Detail Akun - Aretha Beauty
    </h1>

    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6">
        {{-- Notifikasi (optional) --}}
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Data Akun --}}
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" value="{{ $customer->name }}" disabled
                    class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value="{{ $customer->user->email ?? '-' }}" disabled
                    class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                <input type="text" value="{{ $customer->whatsapp ?? '-' }}" disabled
                    class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Membership</label>
                <input type="text" value="{{ $membership ? 'Member' : 'Belum Member' }}" disabled
                    class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2 text-{{ $membership ? 'green-600' : 'red-500' }} font-semibold">
            </div>

            @if ($membership)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Member</label>
                    <input type="text" value="{{ $membership->member_code ?? '-' }}" disabled
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Masa Berlaku Membership</label>
                    <input type="text" value="{{ $membership->expired_at ? $membership->expired_at->format('d-m-Y') : '-' }}" disabled
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>
            @endif
        </div>

        {{-- Tombol --}}
        <div class="mt-6 text-right">
            @if (!$membership)
                <a href="{{ route('customer.akun.membership_form') }}"
                   class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md font-semibold transition">
                    + Daftar Membership
                </a>
            @else
                <button class="bg-gray-300 text-gray-600 px-5 py-2 rounded-md font-semibold cursor-not-allowed" disabled>
                    ✅ Sudah Terdaftar Member
                </button>
            @endif
        </div>
    </div>
</div>
@endsection

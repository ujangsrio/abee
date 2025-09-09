@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Edit Profil - Aretha Beauty
    </h1>

    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6">
        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('customer.profil.update', $customer->id) }}">
            @csrf
            @method('PATCH')

            <div class="space-y-5">
                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>

                {{-- WhatsApp --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $customer->whatsapp) }}"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>

                {{-- Password Lama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                    <input type="password" name="old_password"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>

                {{-- Password Baru --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>
            </div>

            {{-- Tombol Simpan --}}
            <div class="mt-6 text-right">
                <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md font-semibold transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

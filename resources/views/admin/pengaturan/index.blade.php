@extends('layouts.app')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Pengaturan Akun Admin - Aretha Beauty
    </h1>

    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6">
        {{-- Notifikasi sukses --}}
        @if (session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $admin->name) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-400" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $admin->email) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-400" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ubah Kata Sandi</label>
                <input type="password" name="password" placeholder="Masukkan kata sandi baru"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-purple-400">
                <p class="text-sm text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah kata sandi.</p>
            </div>

            <div class="pt-2 text-right">
                <button type="submit"
                        class="bg-purple-700 hover:bg-purple-800 text-white px-5 py-2 rounded-md shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

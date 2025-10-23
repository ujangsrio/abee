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
        
        {{-- Menampilkan error umum dari controller (misal: old_password tidak cocok) --}}
        @if ($errors->any() && !$errors->has('old_password'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <p class="font-bold">Gagal memperbarui profil:</p>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        @if (!in_array($error, ['The old password field is required when the password is present.']))
                           <li>{{ $error }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ACTION diarahkan ke route tanpa ID, METHOD diubah menjadi POST dengan simulasi PUT. enctype dihapus karena tidak ada upload file. --}}
        <form method="POST" action="{{ route('customer.profil.update') }}">
            @csrf
            @method('PUT') 

            <div class="space-y-5">
                
                {{-- Foto Profil (Dihapus) --}}
                
                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- WhatsApp --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $customer->whatsapp) }}"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2 @error('whatsapp') border-red-500 @enderror">
                    @error('whatsapp')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password Lama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama (Isi jika ingin ganti Password)</label>
                    <input type="password" name="old_password"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2 @error('old_password') border-red-500 @enderror">
                    @error('old_password')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    {{-- Error kustom dari controller --}}
                    @error('old_password')
                        @if($message === 'Password lama tidak cocok')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @endif
                    @enderror
                </div>

                {{-- Password Baru --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2 @error('password_confirmation') border-red-500 @enderror">
                    {{-- Password confirmation error biasanya dihandle oleh error 'password' --}}
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

{{-- Script untuk preview foto dihapus --}}
@endsection

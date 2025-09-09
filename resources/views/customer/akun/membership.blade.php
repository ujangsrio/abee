@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
  <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
    Form Pendaftaran Membership - Aretha Beauty
  </h1>

  <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6">
    <form action="{{ route('customer.akun.membership_register') }}" method="POST" class="space-y-5">
      @csrf

      {{-- Nama --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" value="{{ Auth::guard('customer')->user()->name }}" disabled
               class="w-full bg-gray-100 text-sm text-gray-800 px-3 py-2 rounded-md border border-gray-200">
        <input type="hidden" name="name" value="{{ Auth::guard('customer')->user()->name }}">
      </div>

      {{-- Nomor WhatsApp --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
        <input type="text" value="{{ Auth::guard('customer')->user()->customer->whatsapp ?? '-' }}" disabled
               class="w-full bg-gray-100 text-sm text-gray-800 px-3 py-2 rounded-md border border-gray-200">
        <input type="hidden" name="whatsapp" value="{{ Auth::guard('customer')->user()->customer->whatsapp }}">
      </div>

      {{-- Syarat & Ketentuan --}}
      <div class="text-sm text-gray-700">
        <label class="inline-flex items-start leading-relaxed">
          <input type="checkbox" name="agree" value="1" class="mr-2 mt-1" required>
          Saya setuju untuk menjadi member dan menerima <span class="font-semibold">syarat & ketentuan</span> yang berlaku.
        </label>
        @error('agree')
          <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Tombol --}}
      <div class="flex justify-end gap-3 pt-4">
        <a href="{{ route('customer.akun.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-md font-semibold transition">
          Batal
        </a>
        <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-5 py-2 rounded-md font-semibold transition">
          ✅ Daftar Sekarang
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

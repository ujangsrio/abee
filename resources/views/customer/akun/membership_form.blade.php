@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Form Daftar Membership - Aretha Beauty
    </h1>

    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6">
        {{-- Notifikasi error --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customer.akun.membership_register') }}" method="POST" class="space-y-5">
            @csrf

            <div class="text-sm text-gray-700">
                <label class="inline-flex items-start">
                    <input type="checkbox" name="agree" value="1" class="mr-2 mt-1" required>
                    Saya setuju untuk menjadi member dan menerima syarat & ketentuan yang berlaku.
                </label>
                @error('agree')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2 flex justify-end gap-3">
                <a href="{{ route('customer.akun.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-md font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-5 py-2 rounded-md font-semibold transition">
                    ✅ Daftar Membership
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

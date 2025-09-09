@extends('customer.layout')

@section('content')
<div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-md p-8 mt-6">

    <div class="text-center mb-8">
        <img src="{{ asset('images/landingpage/logo.jpg') }}" alt="Aretha Beauty Logo"
             class="mx-auto mb-4 w-28 h-auto rounded shadow">
        <h2 class="text-2xl font-bold text-purple-700 mb-1">Hubungi Kami</h2>
        <p class="text-gray-600 text-sm">Silakan hubungi kami melalui kontak berikut:</p>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <ul class="space-y-5 text-gray-800 text-base">
        <li class="flex items-center gap-4">
            <i class="fas fa-envelope text-purple-600 text-lg"></i>
            <a href="mailto:support@arethabeauty.com" target="_blank" class="hover:underline font-medium">
                support@arethabeauty.com
            </a>
        </li>

        <li class="flex items-center gap-4">
            <i class="fab fa-whatsapp text-green-600 text-lg"></i>
            <a href="https://wa.me/085668077845?text={{ urlencode('Halo, saya ingin bertanya tentang layanan Aretha Beauty.') }}"
               target="_blank" class="hover:underline font-medium">
                Chat via WhatsApp
            </a>
        </li>

        <li class="flex items-center gap-4">
            <i class="fab fa-instagram text-purple-600 text-lg"></i>
            <a href="https://www.instagram.com/aretha_beauty.id/" target="_blank" class="hover:underline font-medium">
                @aretha_beauty.id
            </a>
        </li>
    </ul>
</div>
@endsection

@php
    $url = $getState() ? Storage::disk('public')->url($getState()) : null;
@endphp

@if($url)
    <a href="{{ $url }}" target="_blank" class="block">
        <img 
            src="{{ $url }}" 
            alt="Bukti Transfer" 
            class="w-15 h-15 rounded-lg object-cover shadow-sm hover:shadow-md transition-shadow cursor-pointer"
            onerror="this.src='{{ url('/images/default-bukti.png') }}'"
        >
    </a>
@else
    <span class="text-gray-400 text-sm">Tidak ada</span>
@endif
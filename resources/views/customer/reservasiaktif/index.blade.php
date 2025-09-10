@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
  <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
    Reservasi Aktif - Aretha Beauty
  </h1>

  <div class="max-w-3xl mx-auto">
    @if ($bookings->isEmpty())
      <div class="text-center py-16 text-gray-500">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="No booking" class="w-20 mx-auto mb-4">
        <p class="text-lg font-semibold mb-2">Belum ada reservasi</p>
        <p class="mb-4">Yuk booking layanan sekarang ✨</p>
        <a href="{{ route('customer.layanan') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold">
          + Booking Sekarang
        </a>
      </div>
    @endif

    @foreach ($bookings as $booking)
    <div class="bg-white border border-purple-200 rounded-md shadow-sm p-4 mb-4">
      <div class="flex justify-between items-center cursor-pointer" onclick="toggleDetail('detail{{ $booking->id }}')">
        <h4 class="text-base font-semibold text-purple-700"> {{ $booking->service->nama ?? 'Layanan Tidak Ditemukan' }}</h4>
        @if($booking->tipe_pembayaran === 'full')
        <span class="text-sm font-medium px-2 py-1 rounded bg-green-600 text-white">
           Lunas
        </span>
        @else
        <span class="text-sm font-medium px-2 py-1 rounded 
              {{ in_array($booking->status_dp, ['Lunas', 'Dikonfirmasi']) ? 'bg-green-600 text-white' : 'bg-yellow-300 text-gray-800' }}">
          {{ in_array($booking->status_dp, ['Lunas', 'Dikonfirmasi']) ? 'Lunas DP' : 'Belum DP' }}
        </span>
        @endif
      </div>

      <div id="detail{{ $booking->id }}" class="mt-3 hidden">
        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm text-gray-800">
          <div class="font-medium">ID Reservasi:</div>
          <div>#{{ $booking->id }}</div>

          <div class="font-medium">Tanggal:</div>
          <div>{{ \Carbon\Carbon::parse($booking->date)->translatedFormat('d F Y') }}</div>

          <div class="font-medium">Jam:</div>
          <div>{{ $booking->time }}</div>

          <div class="font-medium">Status Reservasi:</div>
          <div>
            <span class="inline-block text-xs px-2 py-1 rounded bg-purple-500 text-white font-semibold">
              {{ ucfirst($booking->status) }}
            </span>
          </div>

          <div class="font-medium">Status Member:</div>
          <div>
            <span class="inline-block text-xs px-2 py-1 rounded {{ $isMember ? 'bg-green-600 text-white' : 'bg-gray-500 text-white' }}">
              {{ $isMember ? 'Member Aktif' : 'Bukan Member' }}
            </span>
          </div>
        </div>

        {{-- Rincian Biaya --}}
        <div class="mt-4 bg-white-50 border border-white-200 rounded-md p-3">
          <h5 class="font-semibold text-blue-800 mb-2 text-sm">Rincian Biaya</h5>
          <div class="space-y-1 text-xs">
            <div class="flex justify-between">
              <span class="text-gray-600">Harga Layanan:</span>
              <span class="font-medium">Rp {{ number_format($booking->cost_info['base_price'], 0, ',', '.') }}</span>
            </div>
            
            @if($booking->cost_info['discount'] > 0)
            <div class="flex justify-between text-green-600">
              <span>Diskon ({{ $booking->cost_info['promo_name'] }}):</span>
              <span class="font-medium">- Rp {{ number_format($booking->cost_info['discount'], 0, ',', '.') }}</span>
            </div>
            @endif
            
            <div class="flex justify-between font-semibold border-t pt-1 text-sm">
              <span>Total Layanan:</span>
              <span class="text-purple-700">Rp {{ number_format($booking->cost_info['total_after_discount'], 0, ',', '.') }}</span>
            </div>
            
            @if($booking->cost_info['is_full_payment'])
            <div class="flex justify-between text-green-600 font-bold border-t pt-1">
              <span>Status Pembayaran:</span>
              <span>✅ LUNAS (Full Payment)</span>
            </div>
            @else
            <div class="flex justify-between text-orange-600">
              <span>Biaya DP :</span>
              <span class="font-medium">
                @if($booking->cost_info['is_dp_confirmed'])
                  ✅ Rp {{ number_format($booking->cost_info['dp'], 0, ',', '.') }} (Lunas DP)
                @else
                   Rp {{ number_format($booking->cost_info['dp'], 0, ',', '.') }} (Belum Dikonfirmasi)
                @endif
              </span>
            </div>
            
            @if($booking->cost_info['is_dp_confirmed'])
            <div class="flex justify-between font-bold border-t pt-1">
              <span>Sisa Pembayaran:</span>
              <span class="text-red-600">Rp {{ number_format($booking->cost_info['remaining_payment'], 0, ',', '.') }}</span>
            </div>
            @else
            <div class="flex justify-between font-bold border-t pt-1 text-gray-500">
              <span>Sisa Pembayaran:</span>
              <span>Menunggu konfirmasi DP</span>
            </div>
            @endif
            @endif
          </div>
        </div>

        {{-- Tombol --}}
        <div class="mt-3 flex gap-2 flex-wrap">
          @if (strtolower($booking->status) === 'menunggu')
          <form action="{{ route('customer.booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded font-semibold">
              ❌ Batalkan
            </button>
          </form>
          @endif

          @php
            $namaLayanan = $booking->service->nama ?? 'Layanan Tidak Diketahui';
            $tanggal = \Carbon\Carbon::parse($booking->date)->translatedFormat('d F Y');
            $pesanWA = urlencode("Halo Admin Aretha Beauty!\n\nSaya ingin konfirmasi reservasi:\n- Layanan: {$namaLayanan}\n- Tanggal: {$tanggal}\n- Jam: {$booking->time}\n- ID: #{$booking->id}\n\nTerima kasih!");
          @endphp
          <a href="https://wa.me/6285668077845?text={{ $pesanWA }}" target="_blank" class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded font-semibold">
            📲 Hubungi Admin
          </a>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>

<script>
  function toggleDetail(id) {
    document.getElementById(id).classList.toggle('hidden');
  }
</script>
@endsection

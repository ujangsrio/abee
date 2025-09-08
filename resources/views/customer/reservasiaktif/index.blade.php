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
        <span class="text-sm font-medium px-2 py-1 rounded 
              {{ $booking->dp_status === 'Belum' ? 'bg-yellow-300 text-gray-800' : 'bg-green-600 text-white' }}">
          {{ $booking->dp_status === 'Belum' ? 'Belum DP' : 'Lunas DP' }}
        </span>
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
              $pesanWA = urlencode("Halo Admin Aretha Beauty 👋\n\nSaya ingin konfirmasi reservasi berikut:\n\n📝 Layanan: {$namaLayanan}\n📅 Tanggal: {$tanggal}\n🕒 Jam: {$booking->time}\n🆔 ID Reservasi: #{$booking->id}\n\nMohon bantuannya untuk diproses ya, terima kasih 🙏");
          @endphp

            <a 
              href="https://wa.me/6285668077845?text={{ $pesanWA }}" 
              target="_blank" 
              class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded font-semibold">
              📲 Hubungi Admin
            </a>

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

@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Form Booking Layanan
    </h1>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6 space-y-5">
        <form method="POST" action="{{ route('customer.booking.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <input type="hidden" name="service_id" id="service_id">
            <input type="hidden" name="tanggal" id="tanggal_hidden">

            {{-- Nama Customer --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Customer</label>
                <div class="p-2 bg-gray-100 rounded text-gray-800 text-sm">
                    {{ Auth::guard('customer')->user()->name }}
                </div>
            </div>

            {{-- No WhatsApp --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No WhatsApp</label>
                <div class="p-2 bg-gray-100 rounded text-gray-800 text-sm">
                    {{ Auth::guard('customer')->user()->customer->whatsapp ?? '-' }}
                </div>
            </div>

            {{-- Pilih Layanan & Tanggal --}}
            <div>
                <label for="layananSelect" class="block text-sm font-medium text-gray-700 mb-1">Pilih Layanan & Tanggal</label>
                <select id="layananSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm" required>
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($tanggalJam as $tanggal => $jamList)
                        @foreach($jamList as $layanan)
                            <option 
                                value="{{ $layanan->id }}" 
                                data-tanggal="{{ $tanggal }}" 
                                data-nama="{{ $layanan->nama }}">
                                {{ $layanan->nama }} - {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            {{-- Jam Tersedia --}}
            <div>
                <label for="timeSelect" class="block text-sm font-medium text-gray-700 mb-1">Jam Tersedia</label>
                <select id="timeSelect" name="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm" required>
                    <option value="">-- Pilih waktu --</option>
                </select>
                @error('time') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Informasi DP --}}
            <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-3 rounded-md text-sm">
                <p><strong>Perhatian:</strong> Reservasi wajib membayar DP sebesar <strong>Rp50.000</strong>.</p>
                <p>Transfer ke rekening: <strong>1234567890 (BCA) a.n. Aretha Beauty</strong></p>
            </div>

            {{-- Bukti Pembayaran --}}
            <div>
                <label for="bukti_transfer" class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Pembayaran</label>
                <input type="file" id="bukti_transfer" name="bukti_transfer" accept="image/*"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm" required>
                @error('bukti_transfer') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('customer.layanan') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm shadow transition">
                    ← Kembali
                </a>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md text-sm font-semibold transition">
                    Booking Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

{{-- AJAX --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#layananSelect').on('change', function () {
            const selected = $(this).find(':selected');
            const serviceId = selected.val();
            const tanggal = selected.data('tanggal');

            $('#service_id').val(serviceId);
            $('#tanggal_hidden').val(tanggal);

            $.get('{{ route("customer.booking.availableTimes") }}', {
                service_id: serviceId,
                tanggal: tanggal
            }, function (data) {
                const timeSelect = $('#timeSelect');
                timeSelect.empty().append('<option value="">-- Pilih waktu --</option>');
                data.forEach(item => {
                    const waktu = item.jam.substring(0, 5);
                    timeSelect.append(`<option value="${waktu}">${waktu}</option>`);
                });
            });
        });
    });
</script>
@endsection

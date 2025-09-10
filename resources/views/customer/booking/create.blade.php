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

            {{-- Opsi Pembayaran --}}
            <div id="paymentOption" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                <div class="space-y-3">
                    <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_type" value="dp" class="mr-3" checked>
                        <div>
                            <span class="text-sm font-medium">Bayar DP</span>
                            <p class="text-xs text-gray-600">Bayar DP Rp 50.000, sisa dibayar nanti</p>
                        </div>
                    </label>
                    <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="payment_type" value="full" class="mr-3">
                        <div>
                            <span class="text-sm font-medium">Bayar Lunas</span>
                            <p class="text-xs text-gray-600">Bayar total biaya sekarang juga</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Rincian Biaya --}}
            <div id="costBreakdown" class="bg-white-50 border border-white-200 rounded-md p-4" style="display: none;">
                <h3 class="font-semibold text-blue-800 mb-3">Rincian Biaya</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Layanan:</span>
                        <span id="serviceName">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Harga Layanan:</span>
                        <span id="basePrice">Rp -</span>
                    </div>
                    <div id="discountRow" class="flex justify-between text-green-600" style="display: none;">
                        <span>Diskon (<span id="promoName"></span>):</span>
                        <span id="discountAmount">- Rp -</span>
                    </div>
                    <div class="flex justify-between font-semibold border-t pt-2">
                        <span>Total Layanan:</span>
                        <span id="totalAfterDiscount">Rp -</span>
                    </div>
                    <div id="dpRow" class="flex justify-between text-orange-600">
                        <span>Biaya DP :</span>
                        <span id="dpAmount">Rp 50.000</span>
                    </div>
                    <div id="remainingRow" class="flex justify-between text-purple-700 border-t pt-2">
                        <span>Sisa Pembayaran:</span>
                        <span id="remainingPayment">Rp -</span>
                    </div>
                    <div id="totalPaymentRow" class="flex justify-between font-bold text-green-700 border-t pt-2" style="display: none;">
                        <span>Total Pembayaran Sekarang:</span>
                        <span id="totalPaymentNow">Rp -</span>
                    </div>
                </div>
            </div>

            {{-- Informasi Pembayaran --}}
            <div id="paymentInfo" class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-3 rounded-md text-sm">
                <div id="dpInfo">
                    <p><strong>Pembayaran DP:</strong> Bayar DP sebesar <strong>Rp50.000</strong> untuk konfirmasi booking.</p>
                    <p>Transfer ke rekening: <strong>1234567890 (BCA) a.n. Aretha Beauty</strong></p>
                </div>
                <div id="fullInfo" style="display: none;">
                    <p><strong>Pembayaran Lunas:</strong> Bayar total biaya layanan sekarang juga.</p>
                    <p>Transfer ke rekening: <strong>1234567890 (BCA) a.n. Aretha Beauty</strong></p>
                </div>
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
    // Fungsi untuk format rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    $(document).ready(function () {
        $('#layananSelect').on('change', function () {
            const selected = $(this).find(':selected');
            const serviceId = selected.val();
            const tanggal = selected.data('tanggal');

            $('#service_id').val(serviceId);
            $('#tanggal_hidden').val(tanggal);

            if (serviceId) {
                // Ambil jam tersedia
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

                // Hitung biaya total
                $.get('{{ route("customer.booking.calculateCost") }}', {
                    service_id: serviceId
                }, function (data) {
                    // Simpan data untuk perhitungan ulang
                    window.costData = data;
                    
                    // Tampilkan rincian biaya
                    $('#serviceName').text(data.service_name);
                    $('#basePrice').text(formatRupiah(data.base_price));
                    $('#totalAfterDiscount').text(formatRupiah(data.total_after_discount));
                    
                    // Tampilkan diskon jika ada
                    if (data.discount > 0) {
                        $('#discountRow').show();
                        $('#promoName').text(data.promo_name || 'Promo');
                        $('#discountAmount').text(formatRupiah(data.discount));
                    } else {
                        $('#discountRow').hide();
                    }

                    // Update biaya berdasarkan opsi pembayaran
                    updatePaymentDisplay();
                    
                    // Tampilkan opsi pembayaran dan rincian biaya
                    $('#paymentOption').show();

                    // Tampilkan rincian biaya
                    $('#costBreakdown').show();
                }).fail(function() {
                    alert('Gagal memuat informasi biaya. Silakan coba lagi.');
                });
            } else {
                // Sembunyikan rincian biaya jika tidak ada layanan dipilih
                $('#costBreakdown').hide();
                $('#paymentOption').hide();
                $('#timeSelect').empty().append('<option value="">-- Pilih waktu --</option>');
            }
        });

        // Handler untuk perubahan opsi pembayaran
        $('input[name="payment_type"]').on('change', function() {
            updatePaymentDisplay();
        });

        // Fungsi untuk update tampilan pembayaran
        function updatePaymentDisplay() {
            if (!window.costData) return;
            
            const paymentType = $('input[name="payment_type"]:checked').val();
            const data = window.costData;
            
            if (paymentType === 'full') {
                // Pembayaran langsung lunas
                $('#dpRow').hide();
                $('#remainingRow').hide();
                $('#totalPaymentRow').show();
                $('#totalPaymentNow').text(formatRupiah(data.total_after_discount));
                
                // Ubah info pembayaran
                $('#dpInfo').hide();
                $('#fullInfo').show();
            } else {
                // Pembayaran DP
                $('#dpRow').show();
                $('#remainingRow').show();
                $('#totalPaymentRow').hide();
                $('#dpAmount').text(formatRupiah(data.dp));
                $('#remainingPayment').text(formatRupiah(data.remaining_payment));
                
                // Ubah info pembayaran
                $('#fullInfo').hide();
                $('#dpInfo').show();
            }
        }
        
        // Panggil sekali untuk setup awal
        window.updatePaymentDisplay = updatePaymentDisplay;
    });
</script>
@endsection

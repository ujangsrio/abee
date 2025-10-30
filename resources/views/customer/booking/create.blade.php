@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Form Booking Layanan
    </h1>

    {{-- Alerts Global dari Controller (Success/Error) --}}
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

    {{-- MENAMPILKAN SEMUA ERROR VALIDASI --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
            <strong class="font-bold">Gagal Reservasi!</strong>
            <span class="block sm:inline">Harap periksa kembali input Anda di bawah.</span>
        </div>
    @endif

    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6 space-y-5">
        <form method="POST" action="{{ route('customer.booking.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Hidden Fields - Diisi oleh JS -->
            <input type="hidden" name="service_id" id="service_id" value="{{ old('service_id') }}">
            <input type="hidden" name="tanggal" id="tanggal_hidden" value="{{ old('tanggal') }}">
            <input type="hidden" name="tipe_layanan" id="tipe_layanan" value="{{ old('tipe_layanan') }}">

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
                <select id="layananSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm @error('service_id') border-red-500 @enderror @error('tanggal') border-red-500 @enderror" required>
                    <option value="">-- Pilih Layanan --</option>
                    @foreach($tanggalJam as $tanggal => $jamList)
                        @foreach($jamList as $layanan)
                            @php
                                $unique_key = $layanan->id . '_' . $tanggal;
                                $old_key = old('service_id') . '_' . old('tanggal');
                            @endphp
                            <option 
                                value="{{ $layanan->id }}" 
                                data-tanggal="{{ $tanggal }}" 
                                data-nama="{{ $layanan->nama }}"
                                data-unique-key="{{ $unique_key }}"
                                {{ $unique_key == $old_key ? 'selected' : '' }}>
                                {{ $layanan->nama }} - {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
                @error('service_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                @error('tanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Jam Tersedia --}}
            <div>
                <label for="timeSelect" class="block text-sm font-medium text-gray-700 mb-1">Jam Tersedia</label>
                <select id="timeSelect" name="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm @error('time') border-red-500 @enderror" required>
                    <option value="">-- Pilih waktu --</option>
                    @if(old('time'))
                        <option value="{{ old('time') }}" selected>{{ old('time') }} (Dipilih)</option>
                    @endif
                </select>
                @error('time') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tipe Layanan (otomatis) --}}
            <div id="serviceTypeOption" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Layanan</label>
                <div class="relative">
                    <div id="serviceTypeCard" class="p-4 rounded-lg border-2 text-sm font-medium flex items-center gap-3 transition-all duration-200">
                        <div id="serviceTypeIcon" class="text-2xl"></div>
                        <div>
                            <div id="serviceTypeText" class="font-semibold text-lg">-</div>
                            <div id="serviceTypeDesc" class="text-xs opacity-75 mt-1">Pilih layanan untuk melihat tipe</div>
                        </div>
                    </div>
                </div>
                @error('tipe_layanan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Opsi Pembayaran --}}
            <div id="paymentOption" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opsi Pembayaran</label>
                <div class="space-y-3">
                    <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="tipe_pembayaran" value="dp" class="mr-3" 
                               {{ old('tipe_pembayaran', 'dp') == 'dp' ? 'checked' : '' }}>
                        <div>
                            <span class="text-sm font-medium">💳 DP (Down Payment)</span>
                            <p class="text-xs text-gray-600">Bayar DP Rp 50.000, sisa dibayar nanti</p>
                        </div>
                    </label>
                    <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="tipe_pembayaran" value="full" class="mr-3"
                               {{ old('tipe_pembayaran') == 'full' ? 'checked' : '' }}>
                        <div>
                            <span class="text-sm font-medium">💰 Langsung Lunas</span>
                            <p class="text-xs text-gray-600">Bayar total biaya sekarang juga</p>
                        </div>
                    </label>
                </div>
                @error('tipe_pembayaran') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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
                        <span>DP (Uang Muka):</span>
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
            <div id="paymentInfo" class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-3 rounded-md text-sm" style="display: none;">
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
            <div id="buktiTransferSection" style="display: none;">
                <label for="bukti_transfer" class="block text-sm font-medium text-gray-700 mb-1">
                    Upload Bukti Pembayaran <span class="text-red-500">*</span>
                </label>
                <input type="file" id="bukti_transfer" name="bukti_transfer" accept="image/jpeg,image/png,image/jpg"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm @error('bukti_transfer') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                @error('bukti_transfer') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tombol (ATRIBUT DISABLED DIHAPUS agar bisa diklik dan server memproses validasi) --}}
            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('customer.layanan') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm shadow transition">
                    ← Kembali
                </a>
                {{-- FIX: Hapus 'disabled' dari button dan class 'disabled:opacity-50 disabled:cursor-not-allowed' --}}
                <button type="submit" id="submitBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md text-sm font-semibold transition hover:opacity-90">
                    <span id="submitText">Booking Sekarang</span>
                    <span id="loadingText" style="display: none;">Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- AJAX --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // FIX: Ambil nilai old() dari hidden field yang sudah dipopulasi oleh Blade
    const oldServiceId = $('#service_id').val();
    const oldTanggal = $('#tanggal_hidden').val();
    const oldTime = "{{ old('time') }}"; 

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    let availableTimes = [];

    $(document).ready(function () {

        // --- Fungsi ini sekarang hanya berfungsi untuk menampilkan data, tidak lagi menonaktifkan tombol ---
        function setFormDisplay(isVisible) {
            if (isVisible) {
                $('#serviceTypeOption').show();
                $('#paymentOption').show();
                $('#costBreakdown').show();
                $('#paymentInfo').show();
                $('#buktiTransferSection').show();
            } else {
                $('#serviceTypeOption').hide();
                $('#paymentOption').hide();
                $('#costBreakdown').hide();
                $('#paymentInfo').hide();
                $('#buktiTransferSection').hide();
            }
        }
        // --------------------------------------------------------------------------------------------------

        $('#layananSelect').on('change', function () {
            const selected = $(this).find(':selected');
            const serviceId = selected.val();
            const tanggal = selected.data('tanggal');

            // Update hidden fields
            $('#service_id').val(serviceId);
            $('#tanggal_hidden').val(tanggal);

            if (serviceId && tanggal) {
                setFormDisplay(false); // Sembunyikan saat memuat
                $('#timeSelect').empty().append('<option value="">-- Memuat jam... --</option>');
                
                // Ambil jam tersedia
                $.get('{{ route("customer.booking.availableTimes") }}', {
                    service_id: serviceId,
                    tanggal: tanggal
                }, function (data) {
                    availableTimes = data.map(item => item.jam);
                    renderTimeOptions(oldTime); 
                    
                    if (availableTimes.length === 0) {
                        $('#timeSelect').html('<option value="" disabled>Tidak ada jam tersedia</option>');
                    }
                }).fail(function() {
                    $('#timeSelect').html('<option value="" disabled>Gagal memuat jam</option>');
                    console.error('Failed to load available times');
                });

                // Hitung biaya total + tipe layanan
                $.get('{{ route("customer.booking.calculateCost") }}', {
                    service_id: serviceId
                }, function (data) {
                    window.costData = data;

                    // Update cost breakdown
                    $('#serviceName').text(data.service_name);
                    $('#basePrice').text(formatRupiah(data.base_price));
                    $('#totalAfterDiscount').text(formatRupiah(data.total_after_discount));

                    if (data.discount > 0) {
                        $('#discountRow').show();
                        $('#promoName').text(data.promo_name || 'Promo');
                        $('#discountAmount').text('- ' + formatRupiah(data.discount));
                    } else {
                        $('#discountRow').hide();
                    }

                    let tipeLayanan = 'studio'; 
                    // Logika parsing JSON service_type (jika diperlukan)
                    if (data.service_type && typeof data.service_type === 'string') {
                        try {
                            const parsed = JSON.parse(data.service_type);
                            tipeLayanan = Array.isArray(parsed) && parsed.length > 0 ? parsed[0] : 'studio';
                        } catch (e) {
                            tipeLayanan = data.service_type;
                        }
                    } else if (data.service_type && Array.isArray(data.service_type) && data.service_type.length > 0) {
                        tipeLayanan = data.service_type[0];
                    }
                    
                    $('#tipe_layanan').val(tipeLayanan);
                    updateServiceTypeDisplay(tipeLayanan);
                    
                    updatePaymentDisplay();
                    setFormDisplay(true); // Tampilkan setelah data dimuat

                }).fail(function() {
                    console.error('Failed to load cost data');
                    resetForm(); 
                });

            } else {
                resetForm();
            }
        });

        function renderTimeOptions(oldTimeValue = null) {
            const timeSelect = $('#timeSelect');
            timeSelect.empty().append('<option value="">-- Pilih waktu --</option>');

            if (availableTimes.length > 0) {
                availableTimes.forEach(time => {
                    const isSelected = oldTimeValue && oldTimeValue === time ? 'selected' : '';
                    timeSelect.append(`<option value="${time}" ${isSelected}>${time}</option>`);
                });
            }
        }

        $('input[name="tipe_pembayaran"]').on('change', updatePaymentDisplay);

        function updatePaymentDisplay() {
            if (!window.costData) return;
            
            const paymentType = $('input[name="tipe_pembayaran"]:checked').val();
            const data = window.costData;
            
            if (paymentType === 'full') {
                $('#dpRow').hide();
                $('#remainingRow').hide();
                $('#totalPaymentRow').show();
                $('#totalPaymentNow').text(formatRupiah(data.total_after_discount));
                $('#dpInfo').hide();
                $('#fullInfo').show();
            } else {
                $('#dpRow').show();
                $('#remainingRow').show();
                $('#totalPaymentRow').hide();
                $('#dpAmount').text(formatRupiah(data.dp));
                $('#remainingPayment').text(formatRupiah(data.remaining_payment));
                $('#fullInfo').hide();
                $('#dpInfo').show();
            }
        }

        function updateServiceTypeDisplay(tipeLayanan) {
            const serviceTypeConfig = {
                'studio': { icon: '🏢', name: 'Studio', desc: 'Datang ke Salon Kami', bgColor: 'bg-blue-50', borderColor: 'border-blue-200', textColor: 'text-blue-800' },
                'home_service': { icon: '🏠', name: 'Home Service', desc: 'Kami Datang ke Lokasi Anda', bgColor: 'bg-green-50', borderColor: 'border-green-200', textColor: 'text-green-800' }
            };

            const config = serviceTypeConfig[tipeLayanan] || {
                icon: '❓',
                name: tipeLayanan ? tipeLayanan.charAt(0).toUpperCase() + tipeLayanan.slice(1).replace('_', ' ') : 'Layanan',
                desc: 'Tipe layanan dipilih otomatis',
                bgColor: 'bg-gray-50',
                borderColor: 'border-gray-200',
                textColor: 'text-gray-800'
            };

            const card = $('#serviceTypeCard');
            card.removeClass().addClass(`p-4 rounded-lg border-2 text-sm font-medium flex items-center gap-3 transition-all duration-200 ${config.bgColor} ${config.borderColor} ${config.textColor}`);
            
            $('#serviceTypeIcon').text(config.icon);
            $('#serviceTypeText').text(config.name);
            $('#serviceTypeDesc').text(config.desc);
        }

        function resetForm() {
            setFormDisplay(false);
            $('#timeSelect').empty().append('<option value="">-- Pilih waktu --</option>');
            $('#service_id').val('');
            $('#tanggal_hidden').val('');
            $('#tipe_layanan').val('');
            window.costData = null;
        }

        // Form submit handling
        $('form').on('submit', function(e) {
            // Kita tidak mencegah submit di sini agar Laravel dapat memproses validasi
            // Cukup tampilkan loading state
            $('#submitBtn').prop('disabled', true);
            $('#submitText').hide();
            $('#loadingText').show();
            return true;
        });

        // --- FIX INITIALIZATION (MENGEMBALIKAN NILAI LAMA) ---
        if (oldServiceId && oldTanggal) {
            const oldKey = oldServiceId + '_' + oldTanggal;
            const oldOption = $('#layananSelect option[data-unique-key="' + oldKey + '"]');
            
            if (oldOption.length) {
                $('#layananSelect').val(oldServiceId);
                $('#layananSelect').trigger('change');
            } else {
                 resetForm();
            }
        } else {
            resetForm();
        }
    });
</script>
@endsection
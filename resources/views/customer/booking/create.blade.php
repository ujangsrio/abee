@extends('customer.layout')

@section('content')
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Form Booking Layanan
    </h1>
    @if(session('success'))
        <div class="max-w-4xl mx-auto mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-4xl mx-auto mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md shadow-sm text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="max-w-4xl mx-auto mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
            <strong class="font-bold">Gagal Reservasi!</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Informasi Layanan yang Dipilih --}}
    @if(isset($selectedService) && $selectedService)
    <div id="selectedServiceInfo" class="max-w-4xl mx-auto mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($selectedService->gambar && Storage::disk('public')->exists($selectedService->gambar))
                    <img src="{{ Storage::disk('public')->url($selectedService->gambar) }}" 
                         alt="{{ $selectedService->nama }}" 
                         class="w-16 h-16 rounded-lg object-cover">
                @else
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-100 to-pink-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl text-purple-400">
                            @switch($selectedService->kategori)
                                @case('kecantikan') 💄 @break
                                @case('kuku') 💅 @break
                                @case('henna') 🎨 @break
                                @case('bulu_mata') 👁️ @break
                                @case('rambut') 💇 @break
                                @default('lainnya') ✨ @break
                            @endswitch
                        </span>
                    </div>
                @endif
                <div>
                    <h3 class="font-semibold text-blue-800 text-lg">{{ $selectedService->nama }}</h3>
                    <p class="text-sm text-blue-600">{{ $selectedService->deskripsi }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            {{ ucfirst(str_replace('_', ' ', $selectedService->kategori)) }}
                        </span>
                        <span class="text-xs text-blue-600">⏱️ {{ $selectedService->estimasi_durasi }} menit</span>
                        <span class="text-xs text-blue-600">👥 {{ $selectedService->kapasitas_per_slot }} orang</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-lg font-bold text-blue-800">
                    Rp {{ number_format($selectedService->harga, 0, ',', '.') }}
                </div>
                @if($selectedService->is_promo && $selectedService->promo)
                    <div class="text-sm text-green-600">
                        🔥 Diskon {{ $selectedService->promo->diskon }}%
                    </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="max-w-4xl mx-auto mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md shadow-sm text-sm">
        ❌ Tidak ada layanan yang dipilih. Silakan pilih layanan terlebih dahulu.
        <a href="{{ route('customer.layanan') }}" class="font-semibold underline ml-2">Pilih Layanan</a>
    </div>
    @endif

    @if(isset($selectedService) && $selectedService)
    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6 space-y-5">
        <form method="POST" action="{{ route('customer.booking.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Hidden Fields -->
            <input type="hidden" name="service_id" id="service_id" value="{{ $selectedService->id }}">
            <input type="hidden" name="tipe_layanan" id="tipe_layanan" value="{{ old('tipe_layanan', 'studio') }}">

            {{-- Informasi Customer --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            </div>

            {{-- Pilih Tanggal --}}
            <div>
                <label for="tanggalSelect" class="block text-sm font-medium text-gray-700 mb-1">
                    Pilih Tanggal <span class="text-red-500">*</span>
                </label>
                <select id="tanggalSelect" name="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm @error('tanggal') border-red-500 @enderror" required>
                    <option value="">-- Pilih Tanggal --</option>
                    @foreach($availableDates as $tanggal => $info)
                        @php
                            $isOldSelected = old('tanggal') == $tanggal;
                        @endphp
                        <option 
                            value="{{ $tanggal }}" 
                            data-tanggal="{{ $tanggal }}"
                            data-hari-singkat="{{ $info['hari_singkat'] }}"
                            data-jam-buka="{{ $info['jam_buka'] }}"
                            data-jam-tutup="{{ $info['jam_tutup'] }}"
                            {{ $isOldSelected ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($tanggal)->format('d M Y') }} ({{ $info['hari_singkat'] }} {{ $info['jam_buka'] }}-{{ $info['jam_tutup'] }})
                        </option>
                    @endforeach
                </select>
                @error('tanggal') 
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Jam Tersedia --}}
            <div>
                <label for="timeSelect" class="block text-sm font-medium text-gray-700 mb-1">
                    Jam Tersedia <span class="text-red-500">*</span>
                </label>
                <select id="timeSelect" name="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm @error('time') border-red-500 @enderror" required>
                    <option value="">-- Pilih waktu --</option>
                    @if(old('time'))
                        <option value="{{ old('time') }}" selected>{{ old('time') }}</option>
                    @endif
                </select>
                @error('time') 
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Tipe Layanan (Multiple Selection) --}}
            <div id="serviceTypeOption">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Tipe Layanan <span class="text-red-500">*</span>
                </label>
                <div id="serviceTypeSelection" class="space-y-2">
                    @php
                        $tipeLayanan = $selectedService->tipe_layanan;
                        if (is_string($tipeLayanan)) {
                            try {
                                $decoded = json_decode($tipeLayanan, true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    $tipeLayanan = $decoded;
                                }
                            } catch (\Exception $e) {
                                $tipeLayanan = ['studio'];
                            }
                        }
                        
                        if (empty($tipeLayanan)) {
                            $tipeLayanan = ['studio'];
                        }
                        
                        if (!is_array($tipeLayanan)) {
                            $tipeLayanan = [$tipeLayanan];
                        }

                        $serviceTypeConfig = [
                            'studio' => ['icon' => '🏢', 'name' => 'Studio', 'desc' => 'Datang ke Salon Kami'],
                            'home_service' => ['icon' => '🏠', 'name' => 'Home Service', 'desc' => 'Kami Datang ke Lokasi Anda']
                        ];
                    @endphp

                    @foreach($tipeLayanan as $tipe)
                        @php
                            $config = $serviceTypeConfig[$tipe] ?? [
                                'icon' => '❓', 
                                'name' => ucfirst(str_replace('_', ' ', $tipe)), 
                                'desc' => 'Tipe layanan'
                            ];
                            $isChecked = $tipeLayanan[0] === $tipe || old('tipe_layanan') === $tipe;
                        @endphp
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="selected_tipe_layanan" value="{{ $tipe }}" 
                                   class="mr-3 text-purple-600 focus:ring-purple-500 service-type-radio"
                                   {{ $isChecked ? 'checked' : '' }}>
                            <div class="flex items-center gap-3">
                                <span class="text-xl">{{ $config['icon'] }}</span>
                                <div>
                                    <span class="text-sm font-medium">{{ $config['name'] }}</span>
                                    <p class="text-xs text-gray-600 mt-1">{{ $config['desc'] }}</p>
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('tipe_layanan') 
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p> 
                @enderror
            </div>

            {{-- Informasi Layanan --}}
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h3 class="font-semibold text-purple-800 mb-2">Detail Layanan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kategori:</span>
                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $selectedService->kategori)) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Durasi Estimasi:</span>
                        <span class="font-medium">{{ $selectedService->estimasi_durasi }} menit</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kapasitas per Slot:</span>
                        <span class="font-medium">{{ $selectedService->kapasitas_per_slot }} orang</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jadwal Operasional:</span>
                        <span id="serviceSchedule" class="font-medium text-right">-</span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-purple-200">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Deskripsi:</span>
                        <span class="font-medium text-right text-sm">{{ $selectedService->deskripsi }}</span>
                    </div>
                </div>
            </div>

            {{-- Opsi Pembayaran --}}
            <div id="paymentOption">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Opsi Pembayaran <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="tipe_pembayaran" value="dp" class="mr-3 text-purple-600 focus:ring-purple-500" 
                               {{ old('tipe_pembayaran', 'dp') == 'dp' ? 'checked' : '' }}>
                        <div>
                            <span class="text-sm font-medium">💳 DP (Down Payment)</span>
                            <p class="text-xs text-gray-600 mt-1">Bayar DP Rp 50.000, sisa dibayar nanti</p>
                        </div>
                    </label>
                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="tipe_pembayaran" value="full" class="mr-3 text-purple-600 focus:ring-purple-500"
                               {{ old('tipe_pembayaran') == 'full' ? 'checked' : '' }}>
                        <div>
                            <span class="text-sm font-medium">💰 Langsung Lunas</span>
                            <p class="text-xs text-gray-600 mt-1">Bayar total biaya sekarang juga</p>
                        </div>
                    </label>
                </div>
                @error('tipe_pembayaran') 
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p> 
                @enderror
            </div>
            
            {{-- Rincian Biaya --}}
            <div id="costBreakdown" class="bg-white border border-purple-200 rounded-lg p-4">
                <h3 class="font-semibold text-purple-800 mb-3">Rincian Biaya</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Harga Layanan:</span>
                        <span id="basePrice" class="font-medium">Rp {{ number_format($selectedService->harga, 0, ',', '.') }}</span>
                    </div>
                    <div id="discountRow" class="flex justify-between text-green-600" style="display: none;">
                        <span>Diskon (<span id="promoName"></span>):</span>
                        <span id="discountAmount" class="font-medium">- Rp 0</span>
                    </div>
                    <div class="flex justify-between font-semibold border-t pt-2">
                        <span>Total Layanan:</span>
                        <span id="totalAfterDiscount" class="text-purple-700">Rp {{ number_format($selectedService->harga, 0, ',', '.') }}</span>
                    </div>
                    <div id="dpRow" class="flex justify-between text-orange-600">
                        <span>DP (Uang Muka):</span>
                        <span id="dpAmount" class="font-medium">Rp 50.000</span>
                    </div>
                    <div id="remainingRow" class="flex justify-between text-purple-700 border-t pt-2">
                        <span>Sisa Pembayaran:</span>
                        <span id="remainingPayment" class="font-medium">Rp {{ number_format($selectedService->harga - 50000, 0, ',', '.') }}</span>
                    </div>
                    <div id="totalPaymentRow" class="flex justify-between font-bold text-green-700 border-t pt-2" style="display: none;">
                        <span>Total Pembayaran Sekarang:</span>
                        <span id="totalPaymentNow" class="text-green-700">Rp {{ number_format($selectedService->harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Informasi Pembayaran --}}
            <div id="paymentInfo" class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-800 p-4 rounded-md text-sm">
                <div id="dpInfo">
                    <p class="font-semibold mb-2">📋 Informasi Pembayaran DP:</p>
                    <p class="mb-1">• Bayar DP sebesar <strong>Rp 50.000</strong> untuk konfirmasi booking</p>
                    <p class="mb-1">• Transfer ke: <strong>1234567890 (BCA) a.n. Aretha Beauty</strong></p>
                    <p class="mb-0 text-xs mt-2">💡 Sisa pembayaran dapat dilunasi saat layanan berlangsung</p>
                </div>
                <div id="fullInfo" style="display: none;">
                    <p class="font-semibold mb-2">📋 Informasi Pembayaran Lunas:</p>
                    <p class="mb-1">• Bayar total biaya layanan sekarang juga</p>
                    <p class="mb-1">• Transfer ke: <strong>1234567890 (BCA) a.n. Aretha Beauty</strong></p>
                    <p class="mb-0 text-xs mt-2">💡 Pembayaran lunas memudahkan proses layanan</p>
                </div>
            </div>

            {{-- Bukti Pembayaran --}}
            <div id="buktiTransferSection">
                <label for="bukti_transfer" class="block text-sm font-medium text-gray-700 mb-1">
                    Upload Bukti Pembayaran <span class="text-red-500">*</span>
                </label>
                <input type="file" id="bukti_transfer" name="bukti_transfer" accept="image/jpeg,image/png,image/jpg"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm @error('bukti_transfer') border-red-500 @enderror">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                @error('bukti_transfer') 
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p> 
                @enderror
                
                {{-- Preview Gambar --}}
                <div id="imagePreview" class="mt-2" style="display: none;">
                    <img id="previewImage" class="max-w-xs rounded-lg border border-gray-300" src="" alt="Preview Bukti Transfer">
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('customer.layanan') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm shadow transition-colors">
                    ← Kembali ke Layanan
                </a>
                <button type="submit" id="submitBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md text-sm font-semibold transition-colors flex items-center gap-2">
                    <span id="submitText">📅 Booking Sekarang</span>
                    <span id="loadingText" style="display: none;" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="op opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </form>
    </div>
    @endif
</div>

{{-- AJAX --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const oldTime = "{{ old('time') }}";
    const serviceId = "{{ $selectedService->id ?? '' }}";

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    $(document).ready(function () {
        console.log('Service ID:', serviceId);
        console.log('Available dates:', {!! json_encode($availableDates) !!});

        // Event handler untuk select tanggal
        $('#tanggalSelect').on('change', function () {
            const selected = $(this).find(':selected');
            const tanggal = selected.val();

            console.log('Tanggal dipilih:', tanggal);

            if (tanggal) {
                // Update jadwal operasional
                const hariSingkat = selected.data('hari-singkat') || '-';
                const jamBuka = selected.data('jam-buka') || '08:00';
                const jamTutup = selected.data('jam-tutup') || '17:00';
                $('#serviceSchedule').text(hariSingkat + ' ' + jamBuka + '-' + jamTutup);

                // Load jam tersedia
                loadAvailableTimes(tanggal);
            } else {
                $('#timeSelect').empty().append('<option value="">-- Pilih waktu --</option>');
                $('#serviceSchedule').text('-');
            }
        });

        // Load jam tersedia
        function loadAvailableTimes(tanggal) {
            $('#timeSelect').empty().append('<option value="">-- Memuat jam tersedia... --</option>');
            
            console.log('Loading times for:', { service_id: serviceId, tanggal: tanggal });
            
            $.ajax({
                url: '{{ route("customer.booking.availableTimes") }}',
                method: 'GET',
                data: {
                    service_id: serviceId,
                    tanggal: tanggal
                },
                success: function (data) {
                    console.log('Available times loaded:', data);
                    renderTimeOptions(data, oldTime);
                    
                    if (data.length === 0) {
                        $('#timeSelect').html('<option value="" disabled>❌ Tidak ada jam tersedia untuk tanggal ini</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load available times:', error);
                    $('#timeSelect').html('<option value="" disabled>⚠️ Gagal memuat jam tersedia</option>');
                }
            });
        }

        // Render options waktu
        function renderTimeOptions(times, oldTimeValue = null) {
            const timeSelect = $('#timeSelect');
            timeSelect.empty().append('<option value="">-- Pilih waktu --</option>');

            if (times && times.length > 0) {
                times.forEach(slot => {
                    const time = slot.jam || slot;
                    const formattedTime = slot.formatted || time;
                    const isSelected = oldTimeValue && oldTimeValue === time ? 'selected' : '';
                    timeSelect.append(`<option value="${time}" ${isSelected}>🕒 ${formattedTime}</option>`);
                });
            } else {
                timeSelect.append('<option value="" disabled>❌ Tidak ada jam tersedia</option>');
            }
        }

        // Event handler untuk tipe layanan
        $('.service-type-radio').on('change', function() {
            const selectedTipe = $(this).val();
            $('#tipe_layanan').val(selectedTipe);
        });

        // Event handler untuk tipe pembayaran
        $('input[name="tipe_pembayaran"]').on('change', function() {
            updatePaymentDisplay();
        });

        // Update display pembayaran
        function updatePaymentDisplay() {
            const paymentType = $('input[name="tipe_pembayaran"]:checked').val();
            const basePrice = {{ $selectedService->harga ?? 0 }};
            
            if (paymentType === 'full') {
                $('#dpRow').hide();
                $('#remainingRow').hide();
                $('#totalPaymentRow').show();
                $('#totalPaymentNow').text(formatRupiah(basePrice));
                $('#dpInfo').hide();
                $('#fullInfo').show();
            } else {
                $('#dpRow').show();
                $('#remainingRow').show();
                $('#totalPaymentRow').hide();
                $('#dpAmount').text(formatRupiah(50000));
                $('#remainingPayment').text(formatRupiah(basePrice - 50000));
                $('#fullInfo').hide();
                $('#dpInfo').show();
            }
        }

        // Preview gambar upload
        $('#bukti_transfer').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImage').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').hide();
            }
        });

        // Form submit handling
        $('form').on('submit', function(e) {
            // Validasi client-side sederhana
            const tanggal = $('#tanggalSelect').val();
            const time = $('#timeSelect').val();
            const paymentType = $('input[name="tipe_pembayaran"]:checked').val();
            const buktiTransfer = $('#bukti_transfer').val();
            const selectedTipeLayanan = $('input[name="selected_tipe_layanan"]:checked').val();

            if (!tanggal || !time || !paymentType || !buktiTransfer || !selectedTipeLayanan) {
                // Biarkan Laravel handle validasi server-side
                return true;
            }

            // Tampilkan loading state
            $('#submitBtn').prop('disabled', true);
            $('#submitText').hide();
            $('#loadingText').show();
            return true;
        });

        // Initialize form
        if ($('#tanggalSelect').val()) {
            $('#tanggalSelect').trigger('change');
        } else if ($('#tanggalSelect option').length > 1) {
            // Auto-select tanggal pertama jika ada
            $('#tanggalSelect').val($('#tanggalSelect option:eq(1)').val()).trigger('change');
        }

        // Set default tipe layanan
        const defaultTipe = $('input[name="selected_tipe_layanan"]:checked').val();
        if (defaultTipe) {
            $('#tipe_layanan').val(defaultTipe);
        }

        // Initialize payment display
        updatePaymentDisplay();

        // Debug info
        console.log('Available dates count:', $('#tanggalSelect option').length - 1);
    });
</script>
@endsection
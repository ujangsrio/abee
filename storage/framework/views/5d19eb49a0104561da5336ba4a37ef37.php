<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Form Booking Layanan
    </h1>

    
    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md shadow-sm text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md shadow-sm text-sm">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6 space-y-5">
        <form method="POST" action="<?php echo e(route('customer.booking.store')); ?>" enctype="multipart/form-data" class="space-y-5">
            <?php echo csrf_field(); ?>

            <input type="hidden" name="service_id" id="service_id">
            <input type="hidden" name="tanggal" id="tanggal_hidden">
            <input type="hidden" name="tipe_layanan" id="tipe_layanan"> 

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Customer</label>
                <div class="p-2 bg-gray-100 rounded text-gray-800 text-sm">
                    <?php echo e(Auth::guard('customer')->user()->name); ?>

                </div>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No WhatsApp</label>
                <div class="p-2 bg-gray-100 rounded text-gray-800 text-sm">
                    <?php echo e(Auth::guard('customer')->user()->customer->whatsapp ?? '-'); ?>

                </div>
            </div>

            
            <div>
                <label for="layananSelect" class="block text-sm font-medium text-gray-700 mb-1">Pilih Layanan & Tanggal</label>
                <select id="layananSelect" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm" required>
                    <option value="">-- Pilih Layanan --</option>
                    <?php $__currentLoopData = $tanggalJam; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tanggal => $jamList): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $jamList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $layanan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option 
                                value="<?php echo e($layanan->id); ?>" 
                                data-tanggal="<?php echo e($tanggal); ?>" 
                                data-nama="<?php echo e($layanan->nama); ?>">
                                <?php echo e($layanan->nama); ?> - <?php echo e(\Carbon\Carbon::parse($tanggal)->format('d M Y')); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            
            <div>
                <label for="timeSelect" class="block text-sm font-medium text-gray-700 mb-1">Jam Tersedia</label>
                <select id="timeSelect" name="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm" required>
                    <option value="">-- Pilih waktu --</option>
                </select>
                <?php $__errorArgs = ['time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
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
            </div>

            
            <div id="paymentOption" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 mb-2">Opsi Pembayaran</label>
                <div class="space-y-3">
                    <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="tipe_pembayaran" value="dp" class="mr-3" checked>
                        <div>
                            <span class="text-sm font-medium">💳 DP (Down Payment)</span>
                            <p class="text-xs text-gray-600">Bayar DP Rp 50.000, sisa dibayar nanti</p>
                        </div>
                    </label>
                    <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                        <input type="radio" name="tipe_pembayaran" value="full" class="mr-3">
                        <div>
                            <span class="text-sm font-medium">💰 Langsung Lunas</span>
                            <p class="text-xs text-gray-600">Bayar total biaya sekarang juga</p>
                        </div>
                    </label>
                </div>
            </div>
            
            
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

            
            <div id="buktiTransferSection" style="display: none;">
                <label for="bukti_transfer" class="block text-sm font-medium text-gray-700 mb-1">
                    Upload Bukti Pembayaran <span class="text-red-500">*</span>
                </label>
                <input type="file" id="bukti_transfer" name="bukti_transfer" accept="image/jpeg,image/png,image/jpg"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                <?php $__errorArgs = ['bukti_transfer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="flex items-center gap-3 pt-2">
                <a href="<?php echo e(route('customer.layanan')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm shadow transition">
                    ← Kembali
                </a>
                <button type="submit" id="submitBtn" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md text-sm font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <span id="submitText">Booking Sekarang</span>
                    <span id="loadingText" style="display: none;">Memproses...</span>
                </button>
            </div>
        </form>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    let availableTimes = [];

    $(document).ready(function () {
        $('#layananSelect').on('change', function () {
            const selected = $(this).find(':selected');
            const serviceId = selected.val();
            const tanggal = selected.data('tanggal');

            $('#service_id').val(serviceId);
            $('#tanggal_hidden').val(tanggal);

            if (serviceId && tanggal) {
                // Reset form sections
                $('#serviceTypeOption').hide();
                $('#paymentOption').hide();
                $('#costBreakdown').hide();
                $('#paymentInfo').hide();
                $('#buktiTransferSection').hide();
                $('#timeSelect').empty().append('<option value="">-- Memuat jam... --</option>');

                // Ambil jam tersedia
                $.get('<?php echo e(route("customer.booking.availableTimes")); ?>', {
                    service_id: serviceId,
                    tanggal: tanggal
                }, function (data) {
                    availableTimes = data.map(item => item.jam);
                    renderTimeOptions();
                    
                    if (availableTimes.length === 0) {
                        $('#timeSelect').html('<option value="" disabled>Tidak ada jam tersedia</option>');
                    }
                }).fail(function() {
                    $('#timeSelect').html('<option value="" disabled>Gagal memuat jam</option>');
                    console.error('Failed to load available times');
                });

                // Hitung biaya total + tipe layanan
                $.get('<?php echo e(route("customer.booking.calculateCost")); ?>', {
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

                    // Handle service type
                    let tipeLayanan = 'studio'; // default
                    if (data.service_type && Array.isArray(data.service_type) && data.service_type.length > 0) {
                        tipeLayanan = data.service_type[0];
                    } else if (data.service_type && typeof data.service_type === 'string') {
                        try {
                            const parsed = JSON.parse(data.service_type);
                            tipeLayanan = Array.isArray(parsed) && parsed.length > 0 ? parsed[0] : 'studio';
                        } catch (e) {
                            tipeLayanan = data.service_type;
                        }
                    }
                    
                    $('#tipe_layanan').val(tipeLayanan);
                    updateServiceTypeDisplay(tipeLayanan);
                    
                    // Update payment display
                    updatePaymentDisplay();

                    // Show sections
                    $('#serviceTypeOption').show();
                    $('#paymentOption').show();
                    $('#costBreakdown').show();
                    $('#paymentInfo').show();
                    $('#buktiTransferSection').show();

                    validateForm();

                }).fail(function() {
                    console.error('Failed to load cost data');
                    alert('Gagal memuat informasi biaya. Silakan refresh halaman.');
                });

            } else {
                resetForm();
            }
        });

        function renderTimeOptions() {
            const timeSelect = $('#timeSelect');
            timeSelect.empty().append('<option value="">-- Pilih waktu --</option>');

            if (availableTimes.length > 0) {
                availableTimes.forEach(time => {
                    timeSelect.append(`<option value="${time}">${time}</option>`);
                });
            }
        }

        $('input[name="tipe_pembayaran"]').on('change', function() {
            updatePaymentDisplay();
            validateForm();
        });

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
                'studio': {
                    icon: '🏢',
                    name: 'Studio',
                    desc: 'Datang ke Salon Kami',
                    bgColor: 'bg-blue-50',
                    borderColor: 'border-blue-200',
                    textColor: 'text-blue-800'
                },
                'home_service': {
                    icon: '🏠',
                    name: 'Home Service',
                    desc: 'Kami Datang ke Lokasi Anda',
                    bgColor: 'bg-green-50',
                    borderColor: 'border-green-200',
                    textColor: 'text-green-800'
                }
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

        function validateForm() {
            const serviceId = $('#service_id').val();
            const time = $('#timeSelect').val();
            const buktiTransfer = $('#bukti_transfer')[0].files.length > 0;
            
            const isValid = serviceId && time && buktiTransfer;
            $('#submitBtn').prop('disabled', !isValid);
            return isValid;
        }

        function resetForm() {
            $('#serviceTypeOption').hide();
            $('#paymentOption').hide();
            $('#costBreakdown').hide();
            $('#paymentInfo').hide();
            $('#buktiTransferSection').hide();
            $('#timeSelect').empty().append('<option value="">-- Pilih waktu --</option>');
            $('#submitBtn').prop('disabled', true);
        }

        // Check validation on form changes
        $('#timeSelect, #bukti_transfer').on('change', validateForm);

        // Form submit handling
        $('form').on('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                alert('Harap lengkapi semua field yang diperlukan.');
                return false;
            }
            
            $('#submitBtn').prop('disabled', true);
            $('#submitText').hide();
            $('#loadingText').show();
            return true;
        });

        // Initialize
        resetForm();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('customer.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project Abee\abee\resources\views/customer/booking/create.blade.php ENDPATH**/ ?>
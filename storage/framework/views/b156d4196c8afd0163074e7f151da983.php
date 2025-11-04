<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Form Booking Layanan
    </h1>
    <?php if(session('success')): ?>
        <div class="max-w-4xl mx-auto mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-md shadow-sm text-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="max-w-4xl mx-auto mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md shadow-sm text-sm">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="max-w-4xl mx-auto mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
            <strong class="font-bold">Gagal Reservasi!</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <?php if(isset($selectedService) && $selectedService): ?>
    <div id="selectedServiceInfo" class="max-w-4xl mx-auto mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <?php if($selectedService->gambar && Storage::disk('public')->exists($selectedService->gambar)): ?>
                    <img src="<?php echo e(Storage::disk('public')->url($selectedService->gambar)); ?>" 
                         alt="<?php echo e($selectedService->nama); ?>" 
                         class="w-16 h-16 rounded-lg object-cover">
                <?php else: ?>
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-100 to-pink-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl text-purple-400">
                            <?php switch($selectedService->kategori):
                                case ('kecantikan'): ?> 💄 <?php break; ?>
                                <?php case ('kuku'): ?> 💅 <?php break; ?>
                                <?php case ('henna'): ?> 🎨 <?php break; ?>
                                <?php case ('bulu_mata'): ?> 👁️ <?php break; ?>
                                <?php case ('rambut'): ?> 💇 <?php break; ?>
                                <?php default: ?> ✨ <?php break; ?>
                            <?php endswitch; ?>
                        </span>
                    </div>
                <?php endif; ?>
                <div>
                    <h3 class="font-semibold text-blue-800 text-lg"><?php echo e($selectedService->nama); ?></h3>
                    <p class="text-sm text-blue-600"><?php echo e($selectedService->deskripsi); ?></p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                            <?php echo e(ucfirst(str_replace('_', ' ', $selectedService->kategori))); ?>

                        </span>
                        <span class="text-xs text-blue-600">⏱️ <?php echo e($selectedService->estimasi_durasi); ?> menit</span>
                        <span class="text-xs text-blue-600">👥 <?php echo e($selectedService->kapasitas_per_slot); ?> orang</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-lg font-bold text-blue-800">
                    Rp <?php echo e(number_format($selectedService->harga, 0, ',', '.')); ?>

                </div>
                <?php if($selectedService->is_promo && $selectedService->promo): ?>
                    <div class="text-sm text-green-600">
                        🔥 Diskon <?php echo e($selectedService->promo->diskon); ?>%
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="max-w-4xl mx-auto mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-md shadow-sm text-sm">
        ❌ Tidak ada layanan yang dipilih. Silakan pilih layanan terlebih dahulu.
        <a href="<?php echo e(route('customer.layanan')); ?>" class="font-semibold underline ml-2">Pilih Layanan</a>
    </div>
    <?php endif; ?>

    <?php if(isset($selectedService) && $selectedService): ?>
    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6 space-y-5">
        <form method="POST" action="<?php echo e(route('customer.booking.store')); ?>" enctype="multipart/form-data" class="space-y-5">
            <?php echo csrf_field(); ?>

            <!-- Hidden Fields -->
            <input type="hidden" name="service_id" id="service_id" value="<?php echo e($selectedService->id); ?>">
            <input type="hidden" name="tipe_layanan" id="tipe_layanan" value="<?php echo e(old('tipe_layanan', 'studio')); ?>">

            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
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
            </div>

            
            <div>
                <label for="tanggalSelect" class="block text-sm font-medium text-gray-700 mb-1">
                    Pilih Tanggal <span class="text-red-500">*</span>
                </label>
                <select id="tanggalSelect" name="tanggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="">-- Pilih Tanggal --</option>
                    <?php $__currentLoopData = $availableDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tanggal => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isOldSelected = old('tanggal') == $tanggal;
                        ?>
                        <option 
                            value="<?php echo e($tanggal); ?>" 
                            data-tanggal="<?php echo e($tanggal); ?>"
                            data-hari-singkat="<?php echo e($info['hari_singkat']); ?>"
                            data-jam-buka="<?php echo e($info['jam_buka']); ?>"
                            data-jam-tutup="<?php echo e($info['jam_tutup']); ?>"
                            <?php echo e($isOldSelected ? 'selected' : ''); ?>>
                            <?php echo e(\Carbon\Carbon::parse($tanggal)->format('d M Y')); ?> (<?php echo e($info['hari_singkat']); ?> <?php echo e($info['jam_buka']); ?>-<?php echo e($info['jam_tutup']); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['tanggal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                    <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> 
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label for="timeSelect" class="block text-sm font-medium text-gray-700 mb-1">
                    Jam Tersedia <span class="text-red-500">*</span>
                </label>
                <select id="timeSelect" name="time" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm <?php $__errorArgs = ['time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                    <option value="">-- Pilih waktu --</option>
                    <?php if(old('time')): ?>
                        <option value="<?php echo e(old('time')); ?>" selected><?php echo e(old('time')); ?></option>
                    <?php endif; ?>
                </select>
                <?php $__errorArgs = ['time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                    <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> 
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div id="serviceTypeOption">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Tipe Layanan <span class="text-red-500">*</span>
                </label>
                <div id="serviceTypeSelection" class="space-y-2">
                    <?php
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
                    ?>

                    <?php $__currentLoopData = $tipeLayanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $config = $serviceTypeConfig[$tipe] ?? [
                                'icon' => '❓', 
                                'name' => ucfirst(str_replace('_', ' ', $tipe)), 
                                'desc' => 'Tipe layanan'
                            ];
                            $isChecked = $tipeLayanan[0] === $tipe || old('tipe_layanan') === $tipe;
                        ?>
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="radio" name="selected_tipe_layanan" value="<?php echo e($tipe); ?>" 
                                   class="mr-3 text-purple-600 focus:ring-purple-500 service-type-radio"
                                   <?php echo e($isChecked ? 'checked' : ''); ?>>
                            <div class="flex items-center gap-3">
                                <span class="text-xl"><?php echo e($config['icon']); ?></span>
                                <div>
                                    <span class="text-sm font-medium"><?php echo e($config['name']); ?></span>
                                    <p class="text-xs text-gray-600 mt-1"><?php echo e($config['desc']); ?></p>
                                </div>
                            </div>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['tipe_layanan'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                    <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> 
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h3 class="font-semibold text-purple-800 mb-2">Detail Layanan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kategori:</span>
                        <span class="font-medium"><?php echo e(ucfirst(str_replace('_', ' ', $selectedService->kategori))); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Durasi Estimasi:</span>
                        <span class="font-medium"><?php echo e($selectedService->estimasi_durasi); ?> menit</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Kapasitas per Slot:</span>
                        <span class="font-medium"><?php echo e($selectedService->kapasitas_per_slot); ?> orang</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Jadwal Operasional:</span>
                        <span id="serviceSchedule" class="font-medium text-right">-</span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-purple-200">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Deskripsi:</span>
                        <span class="font-medium text-right text-sm"><?php echo e($selectedService->deskripsi); ?></span>
                    </div>
                </div>
            </div>

            
            <div id="paymentOption">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Opsi Pembayaran <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="tipe_pembayaran" value="dp" class="mr-3 text-purple-600 focus:ring-purple-500" 
                               <?php echo e(old('tipe_pembayaran', 'dp') == 'dp' ? 'checked' : ''); ?>>
                        <div>
                            <span class="text-sm font-medium">💳 DP (Down Payment)</span>
                            <p class="text-xs text-gray-600 mt-1">Bayar DP Rp 50.000, sisa dibayar nanti</p>
                        </div>
                    </label>
                    <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="radio" name="tipe_pembayaran" value="full" class="mr-3 text-purple-600 focus:ring-purple-500"
                               <?php echo e(old('tipe_pembayaran') == 'full' ? 'checked' : ''); ?>>
                        <div>
                            <span class="text-sm font-medium">💰 Langsung Lunas</span>
                            <p class="text-xs text-gray-600 mt-1">Bayar total biaya sekarang juga</p>
                        </div>
                    </label>
                </div>
                <?php $__errorArgs = ['tipe_pembayaran'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                    <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> 
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            
            
            <div id="costBreakdown" class="bg-white border border-purple-200 rounded-lg p-4">
                <h3 class="font-semibold text-purple-800 mb-3">Rincian Biaya</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Harga Layanan:</span>
                        <span id="basePrice" class="font-medium">Rp <?php echo e(number_format($selectedService->harga, 0, ',', '.')); ?></span>
                    </div>
                    <div id="discountRow" class="flex justify-between text-green-600" style="display: none;">
                        <span>Diskon (<span id="promoName"></span>):</span>
                        <span id="discountAmount" class="font-medium">- Rp 0</span>
                    </div>
                    <div class="flex justify-between font-semibold border-t pt-2">
                        <span>Total Layanan:</span>
                        <span id="totalAfterDiscount" class="text-purple-700">Rp <?php echo e(number_format($selectedService->harga, 0, ',', '.')); ?></span>
                    </div>
                    <div id="dpRow" class="flex justify-between text-orange-600">
                        <span>DP (Uang Muka):</span>
                        <span id="dpAmount" class="font-medium">Rp 50.000</span>
                    </div>
                    <div id="remainingRow" class="flex justify-between text-purple-700 border-t pt-2">
                        <span>Sisa Pembayaran:</span>
                        <span id="remainingPayment" class="font-medium">Rp <?php echo e(number_format($selectedService->harga - 50000, 0, ',', '.')); ?></span>
                    </div>
                    <div id="totalPaymentRow" class="flex justify-between font-bold text-green-700 border-t pt-2" style="display: none;">
                        <span>Total Pembayaran Sekarang:</span>
                        <span id="totalPaymentNow" class="text-green-700">Rp <?php echo e(number_format($selectedService->harga, 0, ',', '.')); ?></span>
                    </div>
                </div>
            </div>

            
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

            
            <div id="buktiTransferSection">
                <label for="bukti_transfer" class="block text-sm font-medium text-gray-700 mb-1">
                    Upload Bukti Pembayaran <span class="text-red-500">*</span>
                </label>
                <input type="file" id="bukti_transfer" name="bukti_transfer" accept="image/jpeg,image/png,image/jpg"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm <?php $__errorArgs = ['bukti_transfer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                <?php $__errorArgs = ['bukti_transfer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> 
                    <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> 
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                
                
                <div id="imagePreview" class="mt-2" style="display: none;">
                    <img id="previewImage" class="max-w-xs rounded-lg border border-gray-300" src="" alt="Preview Bukti Transfer">
                </div>
            </div>

            
            <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                <a href="<?php echo e(route('customer.layanan')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm shadow transition-colors">
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
    <?php endif; ?>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const oldTime = "<?php echo e(old('time')); ?>";
    const serviceId = "<?php echo e($selectedService->id ?? ''); ?>";

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    $(document).ready(function () {
        console.log('Service ID:', serviceId);
        console.log('Available dates:', <?php echo json_encode($availableDates); ?>);

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
                url: '<?php echo e(route("customer.booking.availableTimes")); ?>',
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
            const basePrice = <?php echo e($selectedService->harga ?? 0); ?>;
            
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('customer.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASVS\Documents\PBL S5\abee\resources\views/customer/booking/create.blade.php ENDPATH**/ ?>
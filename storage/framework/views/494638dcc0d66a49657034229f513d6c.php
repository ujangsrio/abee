<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-500">Nama Pelanggan</h3>
            <p class="mt-1 text-sm text-gray-900"><?php echo e($booking->customer_name); ?></p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-500">Layanan</h3>
            <p class="mt-1 text-sm text-gray-900">
                <?php echo e(\App\Helpers\LayananHelper::getNamaLayanan($booking->service_id)); ?>

            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-500">Tanggal Reservasi</h3>
            <p class="mt-1 text-sm text-gray-900"><?php echo e(\Carbon\Carbon::parse($booking->date)->format('d M Y')); ?></p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-500">Waktu</h3>
            <p class="mt-1 text-sm text-gray-900"><?php echo e($booking->time); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-500">Status</h3>
            <p class="mt-1">
                <?php
                    $statusColor = match($booking->status) {
                        'Menunggu' => 'bg-yellow-100 text-yellow-800',
                        'Dikonfirmasi' => 'bg-blue-100 text-blue-800',
                        'Selesai' => 'bg-green-100 text-green-800',
                        'Dibatalkan' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-800'
                    };
                ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statusColor); ?>">
                    <?php echo e($booking->status); ?>

                </span>
            </p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-500">Status DP</h3>
            <p class="mt-1">
                <?php
                    $dpColor = $booking->status_dp === 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800';
                ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($dpColor); ?>">
                    <?php echo e($booking->status_dp); ?>

                </span>
            </p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-500">Tipe Layanan</h3>
            <p class="mt-1 text-sm text-gray-900">
                <!--[if BLOCK]><![endif]--><?php if(is_array($booking->tipe_layanan)): ?>
                    <?php echo e(implode(', ', array_map(function($type) {
                        return match($type) {
                            'studio' => 'Studio',
                            'home_service' => 'Home Service',
                            default => $type
                        };
                    }, $booking->tipe_layanan))); ?>

                <?php else: ?>
                    <?php echo e($booking->tipe_layanan ?? '-'); ?>

                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-500">Tipe Pembayaran</h3>
            <p class="mt-1 text-sm text-gray-900"><?php echo e($booking->tipe_pembayaran === 'dp' ? 'DP' : 'Lunas'); ?></p>
        </div>
    </div>

    <div>
        <h3 class="text-sm font-medium text-gray-500">Harga Layanan</h3>
        <p class="mt-1 text-lg font-semibold text-green-600">
            Rp <?php echo e(number_format(\App\Helpers\LayananHelper::getHargaLayanan($booking->service_id), 0, ',', '.')); ?>

        </p>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($booking->bukti_transfer): ?>
    <div>
        <h3 class="text-sm font-medium text-gray-500">Bukti Transfer</h3>
        <div class="mt-2">
            <img src="<?php echo e(asset('storage/' . $booking->bukti_transfer)); ?>" 
                 alt="Bukti Transfer" 
                 class="max-w-xs rounded-lg border border-gray-200">
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="grid grid-cols-2 gap-4">
        <div>
            <h3 class="text-sm font-medium text-gray-500">Dibuat Pada</h3>
            <p class="mt-1 text-sm text-gray-900"><?php echo e($booking->created_at->format('d M Y H:i')); ?></p>
        </div>
        
        <div>
            <h3 class="text-sm font-medium text-gray-500">Diupdate Pada</h3>
            <p class="mt-1 text-sm text-gray-900"><?php echo e($booking->updated_at->format('d M Y H:i')); ?></p>
        </div>
    </div>

    <!-- Debug Info (opsional, bisa dihapus setelah testing) -->
    <div class="bg-gray-50 border border-gray-200 rounded-md p-3 mt-4">
        <h4 class="text-xs font-medium text-gray-500 mb-1">Debug Info</h4>
        <p class="text-xs text-gray-600">Service ID: <?php echo e($booking->service_id); ?></p>
    </div>
</div><?php /**PATH D:\kuliah\project abe\abee\resources\views/filament/resources/laporan-resources/modals/view-details.blade.php ENDPATH**/ ?>
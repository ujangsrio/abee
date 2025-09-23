<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Manajemen Reservasi - Aretha Beauty
    </h1>

    <?php if(session('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="max-w-6xl mx-auto bg-white shadow-md rounded-none p-6 border border-purple-100">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-300 rounded-md overflow-hidden">
                <thead class="bg-purple-100 text-black-700 font-medium">
                    <tr>
                        <th class="py-3 px-4 border">No</th>
                        <th class="py-3 px-4 border">Nama Pelanggan</th>
                        <th class="py-3 px-4 border">Kontak</th>
                        <th class="py-3 px-4 border">Layanan</th>
                        <th class="py-3 px-4 border">Tipe Layanan</th>
                        <th class="py-3 px-4 border">Jadwal</th>
                        <th class="py-3 px-4 border">Bukti Transfer</th>
                        <th class="py-3 px-4 border">Status DP</th>
                        <th class="py-3 px-4 border">Status Reservasi</th>
                        <th class="py-3 px-4 border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-purple-50 transition">
                            <td class="py-3 px-4 border text-gray-800"><?php echo e($index + 1); ?></td>
                            <td class="py-3 px-4 border text-gray-800 font-medium"><?php echo e($r->customer_name); ?></td>
                            <td class="py-3 px-4 border text-gray-700"><?php echo e($r->customer->whatsapp ?? '-'); ?></td>
                            <td class="py-3 px-4 border text-gray-700"><?php echo e($r->service->nama ?? '-'); ?></td>
                            <td class="py-3 px-4 border">
                                <?php if($r->tipe_layanan): ?>
                                    <span class="inline-block text-xs px-2 py-1 rounded <?php echo e($r->tipe_layanan === 'home_service' ? 'bg-blue-500 text-white' : 'bg-gray-500 text-white'); ?>">
                                        <?php echo e($r->tipe_layanan === 'home_service' ? ' Home Service' : ' Studio'); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-500 text-sm">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border text-gray-700">
                                <?php echo e(\Carbon\Carbon::parse($r->date . ' ' . $r->time)->translatedFormat('l, d F Y H:i')); ?>

                            </td>
                            <td class="py-3 px-4 border">
                                <?php if($r->bukti_transfer): ?>
                                    <a href="<?php echo e(asset('storage/' . $r->bukti_transfer)); ?>" target="_blank">
                                        <img src="<?php echo e(asset('storage/' . $r->bukti_transfer)); ?>" alt="Bukti Transfer" class="w-20 h-20 object-cover rounded shadow">
                                    </a>
                                <?php else: ?>
                                    <span class="text-sm text-gray-500 italic">Belum ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border">
                                <div class="flex flex-col space-y-2">
                                    <?php
                                        $servicePrice = $r->service->harga ?? 0;
                                        $dpAmount = 50000; // Fixed DP amount
                                        $remainingAmount = 0;

                                        if ($r->tipe_pembayaran === 'dp') {
                                            $remainingAmount = max(0, $servicePrice - $dpAmount);
                                        }

                                        // Logic status pembayaran
                                        if ($r->status === 'Selesai') {
                                            $paymentStatus = 'Lunas';
                                            $paymentClass = 'bg-green-100 text-green-800';
                                        } elseif ($r->tipe_pembayaran === 'full') {
                                            $paymentStatus = 'Lunas';
                                            $paymentClass = 'bg-green-100 text-green-800';
                                        } elseif ($r->status_dp === 'Lunas' && $remainingAmount > 0) {
                                            $paymentStatus = 'DP Lunas';
                                            $paymentClass = 'bg-blue-100 text-blue-800';
                                        } elseif ($r->status_dp === 'Belum') {
                                            $paymentStatus = 'Belum DP';
                                            $paymentClass = 'bg-yellow-100 text-yellow-800';
                                        } else {
                                            $paymentStatus = 'Lunas';
                                            $paymentClass = 'bg-green-100 text-green-800';
                                        }
                                    ?>

                                    <span class="px-2 py-1 rounded text-xs font-medium <?php echo e($paymentClass); ?>">
                                        <?php echo e($paymentStatus); ?>

                                    </span>

                                    
                                    <?php if($remainingAmount > 0 && $r->status_dp === 'Lunas' && $r->status !== 'Selesai'): ?>
                                        <div class="text-xs text-red-600 font-medium">
                                            Kurang: Rp <?php echo e(number_format($remainingAmount, 0, ',', '.')); ?>

                                        </div>
                                    <?php endif; ?>

                                    
                                    <?php if($r->status_dp === 'Belum' && $r->bukti_transfer): ?>
                                        <div class="flex space-x-1">
                                            <form action="<?php echo e(route('admin.reservasi.confirmDp', $r->id)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                                                    ✓ Konfirmasi
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('admin.reservasi.rejectDp', $r->id)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                                    ✗ Tolak
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="py-3 px-4 border">
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    <?php echo e($r->status === 'Menunggu' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($r->status === 'Dikonfirmasi' ? 'bg-green-100 text-green-800' : 
                                       ($r->status === 'Dibatalkan' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'))); ?>">
                                    <?php echo e(ucfirst($r->status)); ?>

                                </span>
                            </td>
                            <td class="py-3 px-4 border">
                                <form action="<?php echo e(route('admin.reservasi.updateStatus', $r->id)); ?>" method="POST" class="flex items-center space-x-2">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <select name="status" class="text-sm border border-gray-300 rounded px-2 py-1">
                                        <option value="Menunggu" <?php echo e($r->status == 'Menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                                        <option value="Dikonfirmasi" <?php echo e($r->status == 'Dikonfirmasi' ? 'selected' : ''); ?>>Dikonfirmasi</option>
                                        <option value="Dibatalkan" <?php echo e($r->status == 'Dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
                                        <option value="Selesai" <?php echo e($r->status == 'Selesai' ? 'selected' : ''); ?>>Selesai</option>
                                    </select>
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center py-6 text-gray-500 italic">Tidak ada reservasi ditemukan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Project Abee\abee\resources\views/admin/reservasi/index.blade.php ENDPATH**/ ?>
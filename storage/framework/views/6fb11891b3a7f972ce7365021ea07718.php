<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <h2 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Histori Layanan - Aretha Beauty
    </h2>

    
    <form method="GET" action="<?php echo e(route('admin.histori.index')); ?>" class="mb-6 flex justify-center">
        <select name="status" onchange="this.form.submit()" 
            class="border border-gray-300 rounded-md px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400">
            <option value="">-- Semua Status --</option>
            <option value="Menunggu" <?php echo e(request('status') == 'Menunggu' ? 'selected' : ''); ?>>Menunggu</option>
            <option value="Dikonfirmasi" <?php echo e(request('status') == 'Dikonfirmasi' ? 'selected' : ''); ?>>Dikonfirmasi</option>
            <option value="Selesai" <?php echo e(request('status') == 'Selesai' ? 'selected' : ''); ?>>Selesai</option>
            <option value="Dibatalkan" <?php echo e(request('status') == 'Dibatalkan' ? 'selected' : ''); ?>>Dibatalkan</option>
        </select>
    </form>

    
    <?php if($histories->isEmpty()): ?>
        <div class="text-center text-gray-500 italic">Tidak ada histori layanan.</div>
    <?php else: ?>
        <div class="max-w-5xl mx-auto space-y-4">
            <?php $__currentLoopData = $histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="border border-purple-100 rounded-md bg-white p-4 hover:bg-gray-50 transition">
                    <h3 class="text-lg font-semibold text-purple-800 mb-1">
                        <?php echo e($item->service->nama ?? 'Layanan Tidak Ditemukan'); ?>

                    </h3>
                    <p class="text-sm text-gray-700">
                        <strong>Tanggal:</strong> <?php echo e(\Carbon\Carbon::parse($item->date)->translatedFormat('d F Y')); ?>

                    </p>
                    <p class="text-sm text-gray-700">
                        <strong>Waktu:</strong> <?php echo e($item->time ?? '-'); ?>

                    </p>
                    <p class="text-sm">
                        <strong>Status:</strong> 
                        <span class="inline-block px-2 py-1 rounded 
                            <?php echo e($item->status === 'Selesai' ? 'bg-green-200 text-green-800' :
                                ($item->status === 'Dibatalkan' ? 'bg-red-200 text-red-800' :
                                ($item->status === 'Dikonfirmasi' ? 'bg-blue-200 text-blue-800' : 'bg-yellow-200 text-yellow-800'))); ?>">
                            <?php echo e($item->status); ?>

                        </span>
                    </p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Project Abee\abee\resources\views/admin/histori/index.blade.php ENDPATH**/ ?>
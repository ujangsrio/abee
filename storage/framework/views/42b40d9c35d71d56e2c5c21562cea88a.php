<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Manajemen Promo - Aretha Beauty
    </h1>

    
    <div class="max-w-4xl mx-auto mb-6 text-right">
        <a href="<?php echo e(route('admin.promo.create')); ?>" class="inline-block bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded shadow-sm">
            + Tambah Promo Baru
        </a>
    </div>

    
    <?php if(session('success')): ?>
        <div class="max-w-4xl mx-auto bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-6">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="max-w-4xl mx-auto space-y-4 mb-8">
        <?php $__empty_1 = true; $__currentLoopData = $promos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="border border-purple-100 bg-white p-5 rounded-md shadow-sm hover:bg-gray-50 transition">
            <h2 class="text-lg font-bold text-purple-800"><?php echo e($promo->nama_promo); ?></h2>
            <p class="text-sm text-gray-700 mb-1"><?php echo e($promo->deskripsi); ?></p>
            <p class="text-sm text-gray-700">Diskon: <strong><?php echo e($promo->diskon); ?>%</strong></p>
            
            <?php if($promo->hanya_member): ?>
                <p class="inline-block text-xs font-medium mt-1 px-2 py-1 bg-yellow-200 text-yellow-800 rounded-sm">Khusus Member</p>
            <?php endif; ?>

            <p class="text-sm text-gray-500 mt-2">
                Berakhir: <?php echo e(\Carbon\Carbon::parse($promo->tanggal_berakhir)->translatedFormat('d F Y')); ?>

            </p>

            <div class="mt-4 flex flex-wrap gap-2">
                <a href="<?php echo e(route('admin.promo.show', $promo->id)); ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm">Lihat Detail</a>
                <a href="<?php echo e(route('admin.promo.edit', $promo->id)); ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">Edit</a>
                <form action="<?php echo e(route('admin.promo.destroy', $promo->id)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus promo ini?')">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Hapus</button>
                </form>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-gray-500 text-center italic">Belum ada promo yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASVS\Documents\PBL S5\fix\abee\resources\views/admin/promo/index.blade.php ENDPATH**/ ?>
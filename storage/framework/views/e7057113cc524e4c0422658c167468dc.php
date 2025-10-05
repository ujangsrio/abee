<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Detail Promo - <?php echo e($promo->nama_promo); ?>

    </h1>

    <div class="max-w-5xl mx-auto bg-white shadow-md border border-purple-100 rounded-sm p-6 space-y-6">
        
        <div>
            <h2 class="text-sm font-medium text-gray-500 mb-1">Nama Promo</h2>
            <p class="text-lg font-semibold text-gray-800"><?php echo e($promo->nama_promo); ?></p>
        </div>

        
        <div>
            <h2 class="text-sm font-medium text-gray-500 mb-1">Deskripsi</h2>
            <p class="text-gray-800"><?php echo e($promo->deskripsi); ?></p>
        </div>

        
        <div>
            <h2 class="text-sm font-medium text-gray-500 mb-1">Diskon</h2>
            <p class="text-gray-800 font-semibold"><?php echo e($promo->diskon); ?>%</p>
        </div>

        
        <?php if($promo->hanya_member): ?>
            <div>
                <span class="inline-block bg-yellow-300 text-black text-xs font-medium px-3 py-1 rounded-full">
                    Promo khusus untuk member
                </span>
            </div>
        <?php endif; ?>

        
        <div>
            <h2 class="text-sm font-medium text-gray-500 mb-1">Tanggal Berakhir</h2>
            <p class="text-gray-800">
                <?php echo e(\Carbon\Carbon::parse($promo->tanggal_berakhir)->translatedFormat('d F Y')); ?>

            </p>
        </div>

        
        <div class="flex justify-between pt-6">
            <a href="<?php echo e(route('admin.promo.index')); ?>" class="text-purple-700 hover:underline text-sm">
                ← Kembali ke daftar promo
            </a>
            <a href="<?php echo e(route('admin.promo.edit', $promo->id)); ?>" class="bg-purple-500 hover:bg-purple-600 text-white px-5 py-2 rounded-md text-sm">
                Edit Promo
            </a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASVS\Documents\PBL S5\fix\abee\resources\views/admin/promo/show.blade.php ENDPATH**/ ?>
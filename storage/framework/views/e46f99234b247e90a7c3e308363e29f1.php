<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Manajemen Pelanggan - Aretha Beauty
    </h1>

    
    <div class="max-w-5xl mx-auto bg-white shadow-md rounded-none p-6 border border-purple-100">
        <h2 class="text-xl font-semibold text-black-700 mb-6">
            Semua Pelanggan Terdaftar
        </h2>

        
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-300 rounded-md overflow-hidden">
                <thead class="bg-purple-100 text-black-700 font-medium">
                    <tr>
                        <th class="px-4 py-3 border">No.</th>
                        <th class="px-4 py-3 border">Nama</th>
                        <th class="px-4 py-3 border">Email</th>
                        <th class="px-4 py-3 border">No. WhatsApp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $pelanggan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-purple-50 transition">
                            <td class="px-4 py-3 border text-gray-800"><?php echo e($index + 1); ?></td>
                            <td class="px-4 py-3 border font-medium text-gray-800"><?php echo e($p->name); ?></td>
                            <td class="px-4 py-3 border text-gray-700"><?php echo e($p->user->email ?? '-'); ?></td>
                            <td class="px-4 py-3 border text-gray-700"><?php echo e($p->whatsapp ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 italic">Belum ada pelanggan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASVS\Documents\PBL S5\fix\abee\resources\views/admin/pelanggan/index.blade.php ENDPATH**/ ?>
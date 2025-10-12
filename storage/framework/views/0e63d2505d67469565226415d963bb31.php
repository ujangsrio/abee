<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Detail Akun - Aretha Beauty
    </h1>

    <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6">
        
        <?php if(session('success')): ?>
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        
        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" value="<?php echo e($customer->name); ?>" disabled
                    class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value="<?php echo e($customer->user->email ?? '-'); ?>" disabled
                    class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
                <input type="text" value="<?php echo e($customer->whatsapp ?? '-'); ?>" disabled
                    class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Membership</label>
                <input type="text" value="<?php echo e($membership ? 'Member' : 'Belum Member'); ?>" disabled
                    class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2 text-<?php echo e($membership ? 'green-600' : 'red-500'); ?> font-semibold">
            </div>

            <?php if($membership): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Member</label>
                    <input type="text" value="<?php echo e($membership->member_code ?? '-'); ?>" disabled
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Masa Berlaku Membership</label>
                    <input type="text" value="<?php echo e($membership->expired_at ? $membership->expired_at->format('d-m-Y') : '-'); ?>" disabled
                        class="w-full bg-gray-100 border border-gray-300 rounded-md px-3 py-2">
                </div>
            <?php endif; ?>
        </div>

        
        <div class="mt-6 text-right">
            <?php if(!$membership): ?>
                <a href="<?php echo e(route('customer.akun.membership_form')); ?>"
                   class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md font-semibold transition">
                    + Daftar Membership
                </a>
            <?php else: ?>
                <button class="bg-gray-300 text-gray-600 px-5 py-2 rounded-md font-semibold cursor-not-allowed" disabled>
                    ✅ Sudah Terdaftar Member
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('customer.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASVS\Documents\PBL S5\abee\resources\views/customer/akun/index.blade.php ENDPATH**/ ?>
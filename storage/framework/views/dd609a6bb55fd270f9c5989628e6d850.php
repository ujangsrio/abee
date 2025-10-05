<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
  <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
    Form Pendaftaran Membership - Aretha Beauty
  </h1>

  <div class="max-w-4xl mx-auto bg-white border border-purple-100 shadow-sm rounded-sm p-6">
    <form action="<?php echo e(route('customer.akun.membership_register')); ?>" method="POST" class="space-y-5">
      <?php echo csrf_field(); ?>

      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
        <input type="text" value="<?php echo e(Auth::guard('customer')->user()->name); ?>" disabled
               class="w-full bg-gray-100 text-sm text-gray-800 px-3 py-2 rounded-md border border-gray-200">
        <input type="hidden" name="name" value="<?php echo e(Auth::guard('customer')->user()->name); ?>">
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp</label>
        <input type="text" value="<?php echo e(Auth::guard('customer')->user()->customer->whatsapp ?? '-'); ?>" disabled
               class="w-full bg-gray-100 text-sm text-gray-800 px-3 py-2 rounded-md border border-gray-200">
        <input type="hidden" name="whatsapp" value="<?php echo e(Auth::guard('customer')->user()->customer->whatsapp); ?>">
      </div>

      
      <div class="text-sm text-gray-700">
        <label class="inline-flex items-start leading-relaxed">
          <input type="checkbox" name="agree" value="1" class="mr-2 mt-1" required>
          Saya setuju untuk menjadi member dan menerima <span class="font-semibold">syarat & ketentuan</span> yang berlaku.
        </label>
        <?php $__errorArgs = ['agree'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div class="flex justify-end gap-3 pt-4">
        <a href="<?php echo e(route('customer.akun.index')); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-5 py-2 rounded-md font-semibold transition">
          Batal
        </a>
        <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-5 py-2 rounded-md font-semibold transition">
          ✅ Daftar Sekarang
        </button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('customer.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project Abee\abee\resources\views/customer/akun/membership.blade.php ENDPATH**/ ?>
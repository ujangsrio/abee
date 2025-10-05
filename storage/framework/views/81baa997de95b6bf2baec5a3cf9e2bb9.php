<?php $__env->startSection('content'); ?>
<div class="p-6 max-w-6xl mx-auto bg-white min-h-screen">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Edit Layanan - <?php echo e($layanan->nama); ?>

    </h1>

    <?php if($errors->any()): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded-md border border-red-300 mb-6">
            <strong>Terjadi kesalahan:</strong>
            <ul class="list-disc pl-5 mt-2 text-sm">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.layanan.update', $layanan->id)); ?>" method="POST" enctype="multipart/form-data" class="space-y-5 bg-white p-6 border border-purple-100 rounded-none shadow-md">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div>
            <label class="block mb-1 font-medium text-gray-700">Nama Layanan</label>
            <input type="text" name="nama" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="<?php echo e(old('nama', $layanan->nama)); ?>" required>
        </div>

        
        <div>
            <label class="block mb-1 font-medium text-gray-700">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" required><?php echo e(old('deskripsi', $layanan->deskripsi)); ?></textarea>
        </div>

        
        <div>
            <label class="block mb-1 font-medium text-gray-700">Harga</label>
            <input type="number" name="harga" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="<?php echo e(old('harga', $layanan->harga)); ?>" required>
        </div>

        
        <div>
            <label class="block mb-1 font-medium text-gray-700">Tanggal</label>
            <input type="date" name="tanggal" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" value="<?php echo e(old('tanggal', $layanan->tanggal)); ?>" required>
        </div>

        
        <div>
            <label class="block mb-1 font-medium text-gray-700">Slot Jam (bisa lebih dari satu)</label>
            <div id="slot-container" class="space-y-2">
                <?php $__currentLoopData = $layanan->slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex gap-2 items-center">
                        <input type="hidden" name="slot_ids[]" value="<?php echo e($slot->id); ?>">
                        <input type="time" name="slots[]" step="60" required class="border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-purple-300" value="<?php echo e($slot->jam); ?>">
                        <button type="button" onclick="this.parentElement.remove()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm">Hapus</button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="button" onclick="tambahSlot()" class="mt-3 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                + Tambah Slot
            </button>
        </div>

        
        <div>
            <label class="block mb-1 font-medium text-gray-700">Gambar Baru (Opsional)</label>
            <input type="file" name="gambar" class="w-full border border-gray-300 rounded-md px-3 py-2">
            <?php if($layanan->gambar): ?>
                <p class="text-sm text-gray-600 mt-2">Gambar saat ini:</p>
                <img src="<?php echo e(asset('storage/photos/' . $layanan->gambar)); ?>" class="w-20 mt-1 rounded-md shadow-sm border">
            <?php endif; ?>
        </div>

        
        <div>
            <label class="block mb-1 font-medium text-gray-700">Tipe Layanan yang Tersedia</label>
            <div class="space-y-2">
                <?php
                    $currentServiceTypes = $layanan->tipe_layanan ?? ['studio', 'home_service'];
                ?>
                <label class="flex items-center">
                    <input type="checkbox" name="tipe_layanan[]" value="studio" class="mr-2" <?php echo e(in_array('studio', $currentServiceTypes) ? 'checked' : ''); ?>>
                    <span> Datang ke Studio</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="tipe_layanan[]" value="home_service" class="mr-2" <?php echo e(in_array('home_service', $currentServiceTypes) ? 'checked' : ''); ?>>
                    <span> Home Service</span>
                </label>
            </div>
            <p class="text-xs text-gray-600 mt-1">Pilih minimal satu tipe layanan yang tersedia</p>
        </div>

        
        <div>
            <label class="block mb-1 font-medium text-gray-700">Pilih Promo (Opsional)</label>
            <select name="promo_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300">
                <option value="">-- Tidak ada promo --</option>
                <?php $__currentLoopData = $promos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($promo->id); ?>" <?php echo e(old('promo_id', $layanan->promo_id) == $promo->id ? 'selected' : ''); ?>>
                        <?php echo e($promo->nama_promo); ?> - <?php echo e($promo->diskon); ?>%
                        <?php if($promo->hanya_member): ?> (Member) <?php endif; ?>
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div class="flex justify-between items-center pt-4">
            <a href="<?php echo e(route('admin.layanan.index')); ?>" class="text-purple-700 hover:underline text-sm">
                ← Kembali
            </a>
            <button type="submit" class="bg-purple-700 hover:bg-purple-800 text-white px-5 py-2 rounded-md">
                Update
            </button>
        </div>
    </form>
</div>


<script>
    function tambahSlot() {
        const container = document.getElementById('slot-container');
        const div = document.createElement('div');
        div.classList.add('flex', 'gap-2', 'items-center');
        div.innerHTML = `
            <input type="hidden" name="slot_ids[]" value="">
            <input type="time" name="slots[]" step="60" class="border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-purple-300" required>
            <button type="button" onclick="this.parentElement.remove()" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md text-sm">Hapus</button>
        `;
        container.appendChild(div);
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project Abee\abee\resources\views/admin/layanan/edit.blade.php ENDPATH**/ ?>
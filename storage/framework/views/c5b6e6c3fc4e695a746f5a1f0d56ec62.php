<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Manajemen Layanan - Aretha Beauty
    </h1>

    <div class="max-w-6xl mx-auto bg-white shadow-md rounded-none p-6 border border-purple-100">
        <a href="<?php echo e(route('admin.layanan.create')); ?>" class="bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded text-sm font-medium mb-6 inline-block">
            + Tambah Layanan
        </a>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-300 rounded-md overflow-hidden">
                <thead class="bg-purple-100 text-black-700 font-medium">
                    <tr>
                        <th class="py-3 px-4 border">ID</th>
                        <th class="py-3 px-4 border">Nama</th>
                        <th class="py-3 px-4 border">Gambar</th>
                        <th class="py-3 px-4 border">Harga</th>
                        <th class="py-3 px-4 border">Tipe Layanan</th>
                        <th class="py-3 px-4 border">Promo</th>
                        <th class="py-3 px-4 border">Slot Tersedia</th>
                        <th class="py-3 px-4 border">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__currentLoopData = $layanans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $layanan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-purple-50 transition">
                            <td class="py-3 px-4 border text-gray-800"><?php echo e($layanan->id); ?></td>
                            <td class="py-3 px-4 border text-gray-800"><?php echo e($layanan->nama); ?></td>
                            <td class="py-3 px-4 border">
                                <?php if($layanan->gambar): ?>
                                    <img src="<?php echo e(asset('storage/photos/' . $layanan->gambar)); ?>" alt="gambar" class="w-16 h-16 object-cover rounded-md shadow-sm">
                                <?php else: ?>
                                    <span class="text-sm text-gray-500 italic">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 border text-gray-800">Rp <?php echo e(number_format($layanan->harga, 0, ',', '.')); ?></td>

                            
                            <td class="py-3 px-4 border">
                                <?php if($layanan->tipe_layanan): ?>
                                    <?php
                                        $serviceTypes = $layanan->tipe_layanan;
                                    ?>
                                    <div class="space-y-1">
                                        <?php $__currentLoopData = $serviceTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="inline-block text-xs px-2 py-1 rounded <?php echo e($type === 'home_service' ? 'bg-blue-500 text-white' : 'bg-gray-500 text-white'); ?>">
                                                <?php echo e($type === 'home_service' ? ' Home Service' : ' Studio'); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-sm text-gray-500 italic">Tidak diset</span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="py-3 px-4 border">
                                <?php if($layanan->promo): ?>
                                    <div class="space-y-1">
                                        <span class="font-semibold text-purple-700"><?php echo e($layanan->promo->nama_promo); ?></span>
                                        <div class="text-sm text-gray-700">
                                            Diskon <?php echo e($layanan->promo->diskon); ?>%
                                            <?php if($layanan->promo->hanya_member): ?>
                                                <span class="ml-2 text-xs bg-yellow-300 text-black px-2 py-1 rounded-sm">Khusus Member</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-sm text-gray-500 italic">Tidak ada</span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="py-3 px-4 border">
                                <?php if($layanan->slots->count()): ?>
                                    <ul class="text-sm text-gray-800 space-y-1">
                                        <?php $__currentLoopData = $layanan->slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li>
                                                <?php echo e(\Carbon\Carbon::parse($slot->tanggal)->format('d/m/Y')); ?> -
                                                <?php echo e(\Carbon\Carbon::parse($slot->jam)->format('H:i')); ?>

                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-sm text-gray-500 italic">Tidak ada slot</span>
                                <?php endif; ?>
                            </td>

                            
                            <td class="py-3 px-4 border whitespace-nowrap">
                                <a href="<?php echo e(route('admin.layanan.edit', $layanan->id)); ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs font-medium mr-2">
                                    Edit
                                </a>
                                <form action="<?php echo e(route('admin.layanan.destroy', $layanan->id)); ?>" method="POST" class="inline-block">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" onclick="return confirm('Yakin hapus layanan ini?')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project Abee\abee\resources\views/admin/layanan/index.blade.php ENDPATH**/ ?>
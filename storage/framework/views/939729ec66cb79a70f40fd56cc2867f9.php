<?php $__env->startSection('content'); ?>
<div class="px-6 py-8 bg-white min-h-screen">
    
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Hai, ✨ <span class="text-purple-600"><?php echo e(Auth::user()->name); ?></span></h2>
        <p class="text-gray-600">Selamat datang di <strong>Aretha Beauty</strong> 💖</p>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 max-w-3xl">
        
        <a href="<?php echo e(route('customer.akun.index')); ?>" class="border border-gray-200 bg-white p-4 shadow-sm hover:bg-gray-50 transition">
            <h3 class="font-semibold text-base text-gray-700">👤 Membership</h3>
            <?php if($membership): ?>
                <p class="mt-1 text-sm text-gray-600">Kode Member: <strong><?php echo e($membership->member_code); ?></strong></p>
            <?php else: ?>
                <p class="mt-1 text-sm text-gray-600">Belum terdaftar 😢<br><strong class="text-purple-600">Daftar yuk!</strong></p>
            <?php endif; ?>
        </a>

        
        <a href="<?php echo e(route('customer.history.index')); ?>" class="border border-gray-200 bg-white p-4 shadow-sm hover:bg-gray-50 transition">
            <h3 class="font-semibold text-base text-gray-700">📅 Total Reservasi</h3>
            <p class="mt-1 text-2xl font-bold text-gray-800"><?php echo e($totalReservations); ?></p>
        </a>
    </div>

    
    <a href="<?php echo e(route('customer.reservasiaktif')); ?>" class="block bg-white border border-purple-200 p-4 mb-6 shadow hover:bg-purple-50 transition">
        <h3 class="text-lg font-bold text-purple-800 mb-2">🕒 Reservasi Aktif</h3>
        <?php if($reservasiAktif->isNotEmpty()): ?>
            <?php $__currentLoopData = $reservasiAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <ul class="text-sm mb-3">
                    <li><strong>Layanan:</strong> <?php echo e($booking->service->nama ?? '-'); ?></li>
                    <li><strong>Tanggal:</strong> <?php echo e(\Carbon\Carbon::parse($booking->date)->format('d M Y')); ?></li>
                    <li><strong>Status:</strong> <span class="font-semibold text-green-600"><?php echo e(ucfirst($booking->status)); ?></span></li>
                </ul>
                <hr class="my-2 border-gray-300">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <p class="text-gray-500 text-sm">Tidak ada reservasi aktif saat ini.</p>
        <?php endif; ?>
    </a>

    
    <div class="bg-purple-50 border-l-4 border-purple-500 p-4 text-gray-800 mb-6">
        🎁 <strong>Promo Minggu Ini:</strong>
        <?php if($promoLayanan): ?>
            <span class="italic">Diskon <?php echo e($promoLayanan->diskon); ?>%</span> untuk <strong><?php echo e($promoLayanan->nama_promo); ?></strong>!
        <?php else: ?>
            Belum ada promo minggu ini.
        <?php endif; ?>
    </div>

    
    <?php if($semuaPromo->count()): ?>
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-3">🔥 Promo Tersedia</h3>
            <ul class="space-y-3">
                <?php $__currentLoopData = $semuaPromo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="bg-white border border-purple-200 p-4 shadow hover:bg-purple-50 transition text-gray-800 cursor-pointer"
                        onclick="window.location.href='<?php echo e(route('customer.layanan')); ?>'">
                        <div class="flex justify-between items-center mb-1 font-semibold">
                            🎁 <?php echo e($promo->nama_promo); ?>

                            <?php if($promo->hanya_member): ?>
                                <span class="bg-purple-600 text-white text-xs px-2 py-1">Member Only</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-sm"><?php echo e($promo->deskripsi); ?></div>
                        <div class="mt-1 text-sm">
                            Diskon: <strong><?php echo e($promo->diskon); ?>%</strong><br>
                            Berlaku sampai: <?php echo e(\Carbon\Carbon::parse($promo->tanggal_berakhir)->translatedFormat('d M Y')); ?>

                        </div>
                        <?php if($promo->hanya_member && !$membership): ?>
                            <div class="mt-2 text-xs text-red-600">❗ Promo ini hanya untuk member. Yuk daftar dulu!</div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    
    <div class="flex flex-wrap gap-3 mb-8">
        <a href="<?php echo e(route('customer.layanan')); ?>" class="bg-purple-500 hover:bg-purple-600 text-white font-bold px-4 py-2 transition">+ Buat Reservasi Baru</a>
        <a href="<?php echo e(route('customer.history.index')); ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold px-4 py-2 transition">Lihat Riwayat Reservasi</a>
    </div>

    
    <div>
        <h3 class="text-lg font-bold text-gray-800 mb-3">💆‍♀️ Daftar Layanan Kami</h3>
        <?php if($layanan->isNotEmpty()): ?>
            <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = $layanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li onclick="window.location.href='<?php echo e(route('customer.layanan')); ?>'" class="bg-white border border-gray-200 shadow p-4 hover:bg-gray-50 transition cursor-pointer">
                        <div class="font-semibold"><?php echo e($item->nama); ?></div>
                        <div class="text-sm text-gray-500">Harga: Rp<?php echo e(number_format($item->harga, 0, ',', '.')); ?></div>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">Belum ada layanan yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('customer.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\kuliah\project abe\abee\resources\views/customer/dashboard.blade.php ENDPATH**/ ?>
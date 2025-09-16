<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aretha Beauty</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-100 to-white text-gray-800 font-sans antialiased h-screen overflow-hidden">

<div class="flex h-full">
    <!-- Sidebar -->
    <aside class="w-64 bg-white/70 backdrop-blur-sm shadow-lg p-6 border-r border-purple-100 hidden md:block fixed top-0 left-0 h-full z-10">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-purple-700 tracking-wide">Aretha Beauty</h1>
            <p class="text-sm text-gray-500">Admin Dashboard</p>
        </div>
        <nav class="flex flex-col space-y-3 text-sm">
            <?php
                $menu = [
                    ['name' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => '🏠'],
                    ['name' => 'Manajemen Layanan', 'route' => 'admin.layanan.index', 'icon' => '💄'],
                    ['name' => 'Data Reservasi', 'route' => 'admin.reservasi.index', 'icon' => '🗓️'],
                    ['name' => 'Promo', 'route' => 'admin.promo.index', 'icon' => '🎁'],
                    ['name' => 'Membership', 'route' => 'admin.membership.index', 'icon' => '👑'],
                    ['name' => 'Pelanggan', 'route' => 'admin.pelanggan.index', 'icon' => '🧍‍♀️'],
                    ['name' => 'Histori Layanan', 'route' => 'admin.histori.index', 'icon' => '🕘'],
                    ['name' => 'Pengaturan', 'route' => 'admin.pengaturan.index', 'icon' => '⚙️'],
                    ['name' => 'Laporan & Export', 'route' => 'admin.laporan.index', 'icon' => '📊'],
                ];
            ?>

            <?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route($item['route'])); ?>"
                   class="flex items-center gap-2 px-3 py-2 rounded transition
                   <?php echo e(request()->routeIs($item['route']) ? 'bg-purple-700 text-white font-semibold' : 'hover:bg-purple-100 text-gray-700'); ?>">
                    <span><?php echo e($item['icon']); ?></span>
                    <span><?php echo e($item['name']); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 flex-1 h-full overflow-y-auto p-6">
        <?php echo $__env->yieldContent('content'); ?>
    </main>
</div>

<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\ASVS\Documents\PBL S5\fix\abee\resources\views/layouts/app.blade.php ENDPATH**/ ?>
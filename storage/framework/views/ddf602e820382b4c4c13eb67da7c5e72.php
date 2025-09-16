<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Customer - Aretha Beauty</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <style>
    /* Scroll area full height minus header */
    .content-wrapper {
      height: calc(100vh - 64px); /* Sesuaikan jika header lebih tinggi */
      overflow-y: auto;
    }
  </style>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

  <!-- Header (Fixed) -->
  <header class="fixed top-0 left-0 right-0 bg-white shadow-md p-4 md:px-10 flex justify-between items-center z-50 h-16">
    <div class="text-purple-600 text-2xl font-bold">Aretha Beauty</div>
    <nav class="space-x-4 text-gray-700 font-medium hidden md:block">
    <a href="<?php echo e(route('customer.layanan')); ?>"
      class="<?php echo e(request()->routeIs('customer.layanan') ? 'text-purple-600 font-semibold underline' : 'hover:text-purple-600'); ?>">
      Layanan
    </a>

    <a href="<?php echo e(route('customer.kontak')); ?>"
      class="<?php echo e(request()->routeIs('customer.kontak') ? 'text-purple-600 font-semibold underline' : 'hover:text-purple-600'); ?>">
      Kontak
    </a>

    <a href="<?php echo e(route('customer.profil')); ?>"
      class="<?php echo e(request()->routeIs('customer.profil') ? 'text-purple-600 font-semibold underline' : 'hover:text-purple-600'); ?>">
      Profil
    </a>
  </nav>
  </header>

  <!-- Main Layout -->
  <div class="flex pt-16"> 

    <!-- Sidebar (Fixed) -->
    <aside class="w-64 bg-white shadow-md p-6 hidden md:block fixed top-16 left-0 bottom-0 overflow-y-auto">
      <nav class="space-y-4 text-gray-700 font-medium">

        <a href="<?php echo e(route('customer.dashboard')); ?>"
           class="flex items-center p-2 rounded-lg 
           <?php echo e(request()->routeIs('customer.dashboard') ? 'bg-purple-100 text-purple-700 font-semibold' : 'hover:bg-purple-50'); ?>">
          <i class="ph ph-house-simple mr-2 text-purple-500"></i> Dashboard
        </a>

        <a href="<?php echo e(route('customer.akun.index')); ?>"
           class="flex items-center p-2 rounded-lg 
           <?php echo e(request()->routeIs('customer.akun.index') ? 'bg-purple-100 text-purple-700 font-semibold' : 'hover:bg-purple-50'); ?>">
          <i class="ph ph-user-circle mr-2 text-purple-500"></i> Detail Akun
        </a>

        <a href="<?php echo e(route('customer.reservasiaktif')); ?>"
           class="flex items-center p-2 rounded-lg 
           <?php echo e(request()->routeIs('customer.reservasiaktif') ? 'bg-purple-100 text-purple-700 font-semibold' : 'hover:bg-purple-50'); ?>">
          <i class="ph ph-calendar-check mr-2 text-purple-500"></i> Reservasi Aktif
        </a>

        <a href="<?php echo e(route('customer.layanan')); ?>"
           class="flex items-center p-2 rounded-lg 
           <?php echo e(request()->routeIs('customer.layanan') ? 'bg-purple-100 text-purple-700 font-semibold' : 'hover:bg-purple-50'); ?>">
          <i class="ph ph-scissors mr-2 text-purple-500"></i> Layanan
        </a>

        <a href="<?php echo e(route('customer.history.index')); ?>"
           class="flex items-center p-2 rounded-lg 
           <?php echo e(request()->routeIs('customer.history.index') ? 'bg-purple-100 text-purple-700 font-semibold' : 'hover:bg-purple-50'); ?>">
          <i class="ph ph-clock-counter-clockwise mr-2 text-purple-500"></i> History Reservasi
        </a>

        <?php if(auth()->guard()->check()): ?>
          <a href="<?php echo e(route('customer.logout')); ?>"
             class="flex items-center p-2 rounded-lg text-red-500 hover:bg-red-100">
            <i class="ph ph-sign-out mr-2"></i> Logout
          </a>
        <?php endif; ?>

        <?php if(auth()->guard()->guest()): ?>
          <a href="<?php echo e(route('login')); ?>"
             class="flex items-center p-2 rounded-lg text-purple-600 hover:bg-purple-100">
            <i class="ph ph-sign-in mr-2"></i> Login
          </a>
        <?php endif; ?>
      </nav>
    </aside>

    <!-- Content Area -->
    <main class="flex-1 md:ml-64">
      <div class="content-wrapper p-6">
        <div class="bg-white p-6 rounded-xl shadow-sm">
          <?php echo $__env->yieldContent('content'); ?>
        </div>
      </div>
    </main>
  </div>

  <!-- Footer -->
  <footer class="bg-white text-center text-sm text-gray-500 py-4 border-t">
    &copy; 2025 Aretha Beauty. All rights reserved.
  </footer>

</body>
</html>
<?php /**PATH C:\Users\ASVS\Documents\PBL S5\fix\abee\resources\views/customer/layout.blade.php ENDPATH**/ ?>
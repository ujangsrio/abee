<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
  <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
    Reservasi Aktif - Aretha Beauty
  </h1>

  <div class="max-w-3xl mx-auto">
    <?php if($bookings->isEmpty()): ?>
      <div class="text-center py-16 text-gray-500">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" alt="No booking" class="w-20 mx-auto mb-4">
        <p class="text-lg font-semibold mb-2">Belum ada reservasi</p>
        <p class="mb-4">Yuk booking layanan sekarang ✨</p>
        <a href="<?php echo e(route('customer.layanan')); ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold">
          + Booking Sekarang
        </a>
      </div>
    <?php endif; ?>

    <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white border border-purple-200 rounded-md shadow-sm p-4 mb-4">
      <div class="flex justify-between items-center cursor-pointer" onclick="toggleDetail('detail<?php echo e($booking->id); ?>')">
        <h4 class="text-base font-semibold text-purple-700"> <?php echo e($booking->service->nama ?? 'Layanan Tidak Ditemukan'); ?></h4>
        <?php if($booking->tipe_pembayaran === 'full'): ?>
        <span class="text-sm font-medium px-2 py-1 rounded bg-green-600 text-white">
          💰 Lunas
        </span>
        <?php else: ?>
        <span class="text-sm font-medium px-2 py-1 rounded 
              <?php echo e(in_array($booking->status_dp, ['Lunas', 'Dikonfirmasi']) ? 'bg-green-600 text-white' : 'bg-yellow-300 text-gray-800'); ?>">
          <?php echo e(in_array($booking->status_dp, ['Lunas', 'Dikonfirmasi']) ? 'Lunas DP' : 'Belum DP'); ?>

        </span>
        <?php endif; ?>
      </div>

      <div id="detail<?php echo e($booking->id); ?>" class="mt-3 hidden">
        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-sm text-gray-800">
          <div class="font-medium">ID Reservasi:</div>
          <div>#<?php echo e($booking->id); ?></div>

          <div class="font-medium">Tanggal:</div>
          <div><?php echo e(\Carbon\Carbon::parse($booking->date)->translatedFormat('d F Y')); ?></div>

          <div class="font-medium">Jam:</div>
          <div><?php echo e($booking->time); ?></div>

          <div class="font-medium">Tipe Layanan:</div>
          <div>
            <span class="inline-block text-xs px-2 py-1 rounded 
              <?php echo e($booking->tipe_layanan === 'home_service' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white'); ?>">
              <?php echo e($booking->tipe_layanan ? ucwords(str_replace('_', ' ', implode(', ', (array)$booking->tipe_layanan))) : 'Tidak Diketahui'); ?>

            </span>
          </div>


          <div class="font-medium">Status Reservasi:</div>
          <div>
            <span class="inline-block text-xs px-2 py-1 rounded bg-purple-500 text-white font-semibold">
              <?php echo e(ucfirst($booking->status)); ?>

            </span>
          </div>

          <div class="font-medium">Status Member:</div>
          <div>
            <span class="inline-block text-xs px-2 py-1 rounded <?php echo e($isMember ? 'bg-green-600 text-white' : 'bg-gray-500 text-white'); ?>">
              <?php echo e($isMember ? 'Member Aktif' : 'Bukan Member'); ?>

            </span>
          </div>
        </div>

        
        <div class="mt-4 bg-blue-50 border border-blue-200 rounded-md p-3">
          <h5 class="font-semibold text-blue-800 mb-2 text-sm">💰 Rincian Biaya</h5>
          <div class="space-y-1 text-xs">
            <div class="flex justify-between">
              <span class="text-gray-600">Harga Layanan:</span>
              <span class="font-medium">Rp <?php echo e(number_format($booking->cost_info['base_price'], 0, ',', '.')); ?></span>
            </div>
            
            <?php if($booking->cost_info['discount'] > 0): ?>
            <div class="flex justify-between text-green-600">
              <span>Diskon (<?php echo e($booking->cost_info['promo_name']); ?>):</span>
              <span class="font-medium">- Rp <?php echo e(number_format($booking->cost_info['discount'], 0, ',', '.')); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="flex justify-between font-semibold border-t pt-1 text-sm">
              <span>Total Layanan:</span>
              <span class="text-purple-700">Rp <?php echo e(number_format($booking->cost_info['total_after_discount'], 0, ',', '.')); ?></span>
            </div>
            
            <?php if($booking->cost_info['is_full_payment']): ?>
            <div class="flex justify-between text-green-600 font-bold border-t pt-1">
              <span>Status Pembayaran:</span>
              <span>✅ LUNAS (Full Payment)</span>
            </div>
            <?php else: ?>
            <div class="flex justify-between text-orange-600">
              <span>DP (Uang Muka):</span>
              <span class="font-medium">
                <?php if($booking->cost_info['is_dp_confirmed']): ?>
                  ✅ Rp <?php echo e(number_format($booking->cost_info['dp'], 0, ',', '.')); ?> (Lunas DP)
                <?php else: ?>
                  ⏳ Rp <?php echo e(number_format($booking->cost_info['dp'], 0, ',', '.')); ?> (Belum Dikonfirmasi)
                <?php endif; ?>
              </span>
            </div>
            
            <?php if($booking->cost_info['is_dp_confirmed']): ?>
            <div class="flex justify-between font-bold border-t pt-1">
              <span>Sisa Pembayaran:</span>
              <span class="text-red-600">Rp <?php echo e(number_format($booking->cost_info['remaining_payment'], 0, ',', '.')); ?></span>
            </div>
            <?php else: ?>
            <div class="flex justify-between font-bold border-t pt-1 text-gray-500">
              <span>Sisa Pembayaran:</span>
              <span>Menunggu konfirmasi DP</span>
            </div>
            <?php endif; ?>
            <?php endif; ?>
          </div>
        </div>

        
        <div class="mt-3 flex gap-2 flex-wrap">
          <?php if(strtolower($booking->status) === 'menunggu'): ?>
          <form action="<?php echo e(route('customer.booking.cancel', $booking->id)); ?>" method="POST" onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?')">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded font-semibold">
              ❌ Batalkan
            </button>
          </form>
          <?php endif; ?>

          <?php
            $namaLayanan = $booking->service->nama ?? 'Layanan Tidak Diketahui';
            $tanggal = \Carbon\Carbon::parse($booking->date)->translatedFormat('d F Y');
            $pesanWA = urlencode("Halo Admin Aretha Beauty!\n\nSaya ingin konfirmasi reservasi:\n- Layanan: {$namaLayanan}\n- Tanggal: {$tanggal}\n- Jam: {$booking->time}\n- ID: #{$booking->id}\n\nTerima kasih!");
          ?>
          <a href="https://wa.me/6285668077845?text=<?php echo e($pesanWA); ?>" target="_blank" class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded font-semibold">
            📲 Hubungi Admin
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>

<script>
  function toggleDetail(id) {
    document.getElementById(id).classList.toggle('hidden');
  }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('customer.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Project Abee\abee\resources\views/customer/reservasiaktif/index.blade.php ENDPATH**/ ?>
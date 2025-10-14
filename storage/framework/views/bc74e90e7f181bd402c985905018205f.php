<?php $__env->startSection('content'); ?>
<?php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::guard('customer')->user();
    $isMember = $user && $user->customer && $user->customer->is_member;
?>

<style>
    .layanan-card {
        border-radius: 15px;
        box-shadow: 0 6px 14px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        background-color: white;
        border: 1px solid #e9d5ff;
    }

    .layanan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .layanan-card img {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .layanan-card .card-body {
        padding: 16px;
        text-align: center;
    }

    .layanan-card h3 {
        font-size: 18px;
        font-weight: 600;
        color: #7e3af2;
        margin-bottom: 6px;
    }

    .layanan-card p {
        font-size: 14px;
        line-height: 1.5;
        color: #555;
    }
</style>

<div class="p-6 min-h-screen bg-white">
    <h1 class="text-2xl font-bold text-center text-black-700 py-4 mb-8 border-b-2 border-purple-200">
        Pilih Layanan - Aretha Beauty
    </h1>

    <div class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__currentLoopData = $layanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $hargaAsli = $item->harga;
                $adaPromo = $item->promo && ($item->promo->hanya_member ? $isMember : true);
                $hargaPromo = $adaPromo ? ($hargaAsli - ($hargaAsli * $item->promo->diskon / 100)) : null;
            ?>

            <div class="layanan-card">
                <img src="<?php echo e(asset('/public/images/photos/' . $item->gambar)); ?>" alt="<?php echo e($item->nama); ?>">

                <div class="card-body space-y-2">
                    
                    <h3><?php echo e($item->nama); ?></h3>

                    
                    <p class="text-sm text-gray-600"><?php echo e($item->deskripsi); ?></p>

                    
                    <?php if($adaPromo): ?>
                        <div class="text-center">
                            <div class="text-purple-700 font-bold text-base">
                                Rp <?php echo e(number_format($hargaPromo, 0, ',', '.')); ?>

                            </div>
                            <div class="text-sm text-gray-400 line-through">
                                Rp <?php echo e(number_format($hargaAsli, 0, ',', '.')); ?>

                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-gray-800 font-semibold text-base">Rp <?php echo e(number_format($hargaAsli, 0, ',', '.')); ?></div>
                    <?php endif; ?>

                    
                    <?php if($item->promo): ?>
                        <?php if($item->promo->hanya_member): ?>
                            <div class="inline-block text-[12px] font-medium px-2 py-0.5 rounded
                                <?php echo e($isMember ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                <?php echo e($isMember ? 'Diskon Member ' . $item->promo->diskon . '%' : 'Promo Khusus Member'); ?>

                            </div>
                        <?php else: ?>
                            <div class="inline-block text-[12px] bg-purple-100 text-purple-800 font-medium px-2 py-0.5 rounded">
                                Diskon <?php echo e($item->promo->diskon); ?>%
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="inline-block text-[12px] bg-gray-100 text-gray-500 px-2 py-0.5 rounded">
                            Tidak ada promo
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="mt-10 text-center">
        <a href="<?php echo e(route('customer.booking.create')); ?>"
           class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2 rounded-md font-semibold transition">
            Booking Sekarang
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('customer.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\kuliah\project abe\abee\resources\views/customer/layanan/index.blade.php ENDPATH**/ ?>
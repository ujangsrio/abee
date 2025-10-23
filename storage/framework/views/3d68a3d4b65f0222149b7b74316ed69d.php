<div class="p-6 text-center">
    <!--[if BLOCK]><![endif]--><?php if($bukti_transfer && Storage::disk('public')->exists($bukti_transfer)): ?>
        <div class="flex flex-col items-center space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Bukti Transfer</h2>

            <div class="relative">
                <img 
                    src="<?php echo e(asset('storage/' . $bukti_transfer)); ?>" 
                    alt="Bukti Transfer" 
                    class="rounded-xl shadow-lg max-h-[600px] w-auto object-contain border border-gray-200 transition-transform duration-200 hover:scale-105"
                >
                
            </div>

           
        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center space-y-3 py-10">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-photo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-16 h-16 text-gray-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            <h3 class="text-lg font-semibold text-gray-700">Bukti Transfer Tidak Ditemukan</h3>
            <p class="text-gray-500 text-sm">Pastikan pelanggan telah mengunggah bukti transfer dengan benar.</p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\Project Abee\abee\resources\views/filament/components/bukti-transfer-preview.blade.php ENDPATH**/ ?>
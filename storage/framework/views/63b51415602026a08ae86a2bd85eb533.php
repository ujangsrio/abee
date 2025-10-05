<div
    <?php echo e($attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)); ?>

>
    <?php echo e($getChildComponentContainer()); ?>

</div>
<?php /**PATH C:\Users\ASVS\Documents\PBL S5\fix\abee\vendor\filament\forms\resources\views/components/grid.blade.php ENDPATH**/ ?>
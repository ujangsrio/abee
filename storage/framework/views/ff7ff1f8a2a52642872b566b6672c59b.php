<?php
    $url = asset('storage/' . $getState());
?>

<!--[if BLOCK]><![endif]--><?php if($getState()): ?>
    <button
        x-data="{ open: false }"
        @click="open = true"
        class="focus:outline-none"
    >
        <img
            src="<?php echo e($url); ?>"
            alt="Preview"
            class="w-16 h-16 object-cover rounded-lg shadow cursor-pointer hover:scale-105 transition-transform duration-200"
        >

        <!-- Modal -->
        <div
            x-show="open"
            x-transition
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
        >
            <div class="relative">
                <img src="<?php echo e($url); ?>" class="max-h-[90vh] rounded-lg shadow-lg">
                <button
                    @click="open = false"
                    class="absolute top-2 right-2 text-white text-2xl font-bold hover:text-gray-300"
                >&times;</button>
            </div>
        </div>
    </button>
<?php else: ?>
    <span class="text-gray-400">Tidak ada gambar</span>
<?php endif; ?><!--[if ENDBLOCK]><![endif]-->
<?php /**PATH C:\Users\ASVS\Documents\PBL S5\abee\resources\views/filament/components/image-preview.blade.php ENDPATH**/ ?>
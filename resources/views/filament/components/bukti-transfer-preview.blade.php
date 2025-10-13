<div class="p-6 text-center">
    @if ($bukti_transfer && Storage::disk('public')->exists($bukti_transfer))
        <div class="flex flex-col items-center space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Bukti Transfer</h2>

            <div class="relative">
                <img 
                    src="{{ asset('storage/' . $bukti_transfer) }}" 
                    alt="Bukti Transfer" 
                    class="rounded-xl shadow-lg max-h-[600px] w-auto object-contain border border-gray-200 transition-transform duration-200 hover:scale-105"
                >
                
            </div>

           
        </div>
    @else
        <div class="flex flex-col items-center justify-center space-y-3 py-10">
            <x-heroicon-o-photo class="w-16 h-16 text-gray-400" />
            <h3 class="text-lg font-semibold text-gray-700">Bukti Transfer Tidak Ditemukan</h3>
            <p class="text-gray-500 text-sm">Pastikan pelanggan telah mengunggah bukti transfer dengan benar.</p>
        </div>
    @endif
</div>

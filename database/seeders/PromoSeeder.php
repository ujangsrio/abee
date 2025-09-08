<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promo;

class PromoSeeder extends Seeder
{
    public function run()
    {
        Promo::create([
            'nama_promo' => 'Promo Facial Cerah Lebaran',
            'deskripsi' => 'Diskon 30% untuk layanan Facial selama bulan Ramadhan.',
            'tanggal_berakhir' => '2025-04-21',
        ]);

        Promo::create([
            'nama_promo' => 'Diskon 20% Hair Spa Weekend',
            'deskripsi' => 'Promo spesial weekend, diskon 20% untuk Hair Spa setiap hari Sabtu dan Minggu.',
            'tanggal_berakhir' => '2025-04-30',
        ]);
    }
}

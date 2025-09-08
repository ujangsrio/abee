<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Layanan;

class LayananSeeder extends Seeder
{
    public function run()
    {
        Layanan::insert([
            [
                'nama_layanan' => 'Nail Art',
                'harga' => 350000,
                'kategori' => 'Nail',
            ],
            [
                'nama_layanan' => 'Make Up',
                'harga' => 500000,
                'kategori' => 'Face',
            ],
            [
                'nama_layanan' => 'Henna',
                'harga' => 200000,
                'kategori' => 'Body',
            ],
            [
                'nama_layanan' => 'Lash Lift',
                'harga' => 100000,
                'kategori' => 'Eye',
            ],
        ]);
    }
}

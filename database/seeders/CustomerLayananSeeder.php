<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerLayanan;

class CustomerLayananSeeder extends Seeder
{
    public function run(): void
    {
        $layananList = [
            ['nama' => 'Make Up', 'harga' => 150000, 'gambar' => 'makeup.jpg'],
            ['nama' => 'Nail Art', 'harga' => 120000, 'gambar' => 'nail_art.jpg'],
            ['nama' => 'Henna', 'harga' => 100000, 'gambar' => 'henna.jpg'],
            ['nama' => 'Lash Lift', 'harga' => 130000, 'gambar' => 'lash_lift.jpg'],
            ['nama' => 'Gel Extension', 'harga' => 170000, 'gambar' => 'gel_exten.jpg'],
            ['nama' => 'Acrylic Nails', 'harga' => 200000, 'gambar' => 'acrylic.jpg'],
            ['nama' => 'Press On Nails', 'harga' => 90000, 'gambar' => 'press_on_nail.jpg'],
            ['nama' => 'Soft Gel Tips', 'harga' => 110000, 'gambar' => 'soft_gel_tips.jpg'],
        ];

        foreach ($layananList as $layanan) {
            CustomerLayanan::create([
                'nama' => $layanan['nama'],
                'deskripsi' => 'Layanan ' . $layanan['nama'] . ' berkualitas tinggi dari Aretha Beauty.',
                'harga' => $layanan['harga'],
                'gambar' => $layanan['gambar'],
            ]);
        }
    }
}

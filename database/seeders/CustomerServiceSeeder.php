<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerService;

class CustomerServiceSeeder extends Seeder
{
    public function run()
    {
        CustomerService::truncate(); // Kosongkan dulu datanya

        $layananList = [
            ['name' => 'Make Up'],
            ['name' => 'Nail Art'],
            ['name' => 'Henna'],
            ['name' => 'Lash Lift'],
            ['name' => 'Gel Extension'],
            ['name' => 'Acrylic Nails'],
            ['name' => 'Press On Nails'],
            ['name' => 'Soft Gel Tips'],
        ];

        foreach ($layananList as $layanan) {
            CustomerService::create($layanan);
        }
    }
}

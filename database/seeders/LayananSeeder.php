<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('layanans')->insert([
            [
                'nama' => 'Nail Art',
                'harga' => 350000,
                'tanggal' => Carbon::now(),
            ],
            [
                'nama' => 'Make Up',
                'harga' => 500000,
                'tanggal' => Carbon::now(),
            ],
            [
                'nama' => 'Henna',
                'harga' => 200000,
                'tanggal' => Carbon::now(),
            ],
            [
                'nama' => 'Lash Lift',
                'harga' => 100000,
                'tanggal' => Carbon::now(),
            ],
        ]);
    }
}

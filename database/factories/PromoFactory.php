<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PromoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_promo' => $this->faker->sentence(3),
            'deskripsi' => $this->faker->paragraph(),
            'diskon' => $this->faker->numberBetween(5, 50),
            'tanggal_berakhir' => $this->faker->dateTimeBetween('+1 week', '+2 months')->format('Y-m-d'),
            'status' => 'aktif',
        ];
    }
}

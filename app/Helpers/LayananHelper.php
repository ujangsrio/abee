<?php

namespace App\Helpers;

class LayananHelper
{
    public static function getNamaLayanan($serviceId)
    {
        $mapping = [
            1 => 'Henna',
            2 => 'Make Up',
            3 => 'Nail Art',
            4 => 'Lash Lift',
            5 => 'Gel Extension',
            6 => 'Acrylic Nails',
            7 => 'Press On Nails',
            8 => 'Soft Gel Tips',
        ];

        return $mapping[$serviceId] ?? 'Layanan ID: ' . $serviceId;
    }

    public static function getHargaLayanan($serviceId)
    {
        $mapping = [
            1 => 100000,   // Henna
            2 => 150000,   // Make Up
            3 => 120000,   // Nail Art
            4 => 130000,   // Lash Lift
            5 => 170000,   // Gel Extension
            6 => 200000,   // Acrylic Nails
            7 => 90000,    // Press On Nails
            8 => 110000,   // Soft Gel Tips
        ];

        return $mapping[$serviceId] ?? 0;
    }

    public static function getAllLayanan()
    {
        return [
            1 => ['nama' => 'Henna', 'harga' => 100000],
            2 => ['nama' => 'Make Up', 'harga' => 150000],
            3 => ['nama' => 'Nail Art', 'harga' => 120000],
            4 => ['nama' => 'Lash Lift', 'harga' => 130000],
            5 => ['nama' => 'Gel Extension', 'harga' => 170000],
            6 => ['nama' => 'Acrylic Nails', 'harga' => 200000],
            7 => ['nama' => 'Press On Nails', 'harga' => 90000],
            8 => ['nama' => 'Soft Gel Tips', 'harga' => 110000],
        ];
    }
}

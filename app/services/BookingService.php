<?php

namespace App\Services;

use App\Models\Booking;

class BookingService
{
    public function hitungDP($harga)
    {
        return [
            'dp'   => $harga * 0.5,
            'sisa' => $harga * 0.5
        ];
    }

    public function hitungLunas($harga)
    {
        return [
            'total' => $harga,
            'sisa'  => 0
        ];
    }

    public function cekSlotBentrok($tanggal, $jam)
    {
        return Booking::where('tanggal', $tanggal)
                      ->where('jam', $jam)
                      ->exists();
    }

    public function tentukanTipe($layanan)
    {
        return $layanan->jenis ?? 'studio';
    }
}

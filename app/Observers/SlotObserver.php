<?php

namespace App\Observers;

use App\Models\Slot;

class SlotObserver
{
    public function creating(Slot $slot)
    {
        // Jika tanggal belum diisi, ambil dari layanan parent
        if (empty($slot->tanggal) && $slot->layanan) {
            $slot->tanggal = $slot->layanan->tanggal;
        }
    }

    public function updating(Slot $slot)
    {
        // Jika tanggal belum diisi, ambil dari layanan parent
        if (empty($slot->tanggal) && $slot->layanan) {
            $slot->tanggal = $slot->layanan->tanggal;
        }
    }
}

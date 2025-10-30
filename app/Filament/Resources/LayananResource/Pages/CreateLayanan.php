<?php

namespace App\Filament\Resources\LayananResource\Pages;

use App\Filament\Resources\LayananResource;
use App\Models\Slot; // Import ini tidak lagi digunakan, tapi biarkan saja
use Filament\Resources\Pages\CreateRecord;

class CreateLayanan extends CreateRecord
{
    protected static string $resource = LayananResource::class;

    protected function afterCreate(): void
    {
        // Logika slot otomatis lama telah dihapus karena sekarang menggunakan 
        // sistem jadwal berulang (recurring_schedule) yang tidak bergantung pada 
        // field 'tanggal' dan 'jam' sederhana.
        return;
    }
}
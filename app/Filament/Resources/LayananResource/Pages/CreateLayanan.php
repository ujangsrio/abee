<?php

namespace App\Filament\Resources\LayananResource\Pages;

use App\Filament\Resources\LayananResource;
use App\Models\Slot;
use Filament\Resources\Pages\CreateRecord;

class CreateLayanan extends CreateRecord
{
    protected static string $resource = LayananResource::class;

    protected function afterCreate(): void
    {
        // Buat slot otomatis sesuai tanggal & jam utama
        if ($this->record->tanggal && $this->record->jam) {
            Slot::create([
                'layanan_id' => $this->record->id,
                'tanggal' => $this->record->tanggal,
                'jam' => $this->record->jam,
            ]);
        }
    }
}

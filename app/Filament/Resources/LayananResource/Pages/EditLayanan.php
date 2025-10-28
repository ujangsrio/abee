<?php

namespace App\Filament\Resources\LayananResource\Pages;

use App\Filament\Resources\LayananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Layanan;

class EditLayanan extends EditRecord
{
    protected static string $resource = LayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        /** @var Layanan $layanan */
        $layanan = $this->record;

        // Panggilan $layanan->autoCreateOrUpdateSlot() telah dihapus di sini 
        // karena logika lama tersebut sudah tidak digunakan dan menyebabkan error.
        // Logika jadwal sekarang dihandle oleh recurring_schedule dan exception_schedule.
    }
}
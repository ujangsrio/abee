<?php

namespace App\Filament\Resources\LayananResource\Pages;

use App\Filament\Resources\LayananResource;
use App\Models\Layanan;
use App\Models\LayananJadwalBulanan;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateLayanan extends CreateRecord
{
    protected static string $resource = LayananResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Handle waktu operasional
        $data['waktu_operasional'] = [
            'periode_mulai' => $data['periode_mulai'] ?? null,
            'periode_selesai' => $data['periode_selesai'] ?? null,
            'jam_buka_default' => $data['jam_buka_default'] ?? '08:00',
            'jam_tutup_default' => $data['jam_tutup_default'] ?? '17:00',
            'hari_operasional' => $data['hari_operasional'] ?? [],
        ];

        // Hapus field individual
        unset(
            $data['periode_mulai'],
            $data['periode_selesai'],
            $data['jam_buka_default'],
            $data['jam_tutup_default'],
            $data['hari_operasional']
        );

        return $data;
    }

    protected function afterCreate(): void
    {
        $layanan = $this->record;
        $data = $this->form->getState();

        // Buat jadwal bulanan
        if (isset($data['hari_operasional']) && is_array($data['hari_operasional'])) {
            foreach ($data['hari_operasional'] as $hari) {
                LayananJadwalBulanan::create([
                    'layanan_id' => $layanan->id,
                    'hari' => $hari,
                    'jam_buka' => $data['jam_buka_default'] ?? '08:00',
                    'jam_tutup' => $data['jam_tutup_default'] ?? '17:00',
                    'is_aktif' => true,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

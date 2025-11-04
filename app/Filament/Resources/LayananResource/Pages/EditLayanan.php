<?php

namespace App\Filament\Resources\LayananResource\Pages;

use App\Filament\Resources\LayananResource;
use App\Models\Layanan;
use App\Models\LayananJadwalBulanan;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditLayanan extends EditRecord
{
    protected static string $resource = LayananResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $layanan = $this->record;

        // Isi field individual dari waktu_operasional JSON
        $waktuOperasional = $layanan->waktu_operasional ?? [];

        $data['periode_mulai'] = $waktuOperasional['periode_mulai'] ?? null;
        $data['periode_selesai'] = $waktuOperasional['periode_selesai'] ?? null;
        $data['jam_buka_default'] = $waktuOperasional['jam_buka_default'] ?? '08:00';
        $data['jam_tutup_default'] = $waktuOperasional['jam_tutup_default'] ?? '17:00';
        $data['hari_operasional'] = $waktuOperasional['hari_operasional'] ?? [];

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function afterSave(): void
    {
        $layanan = $this->record;
        $data = $this->form->getState();

        // Update jadwal bulanan
        LayananJadwalBulanan::where('layanan_id', $layanan->id)->delete();

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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action) {
                    // Hapus gambar saat delete (sama seperti booking controller)
                    $layanan = $this->record;

                    if ($layanan->gambar && Storage::disk('public')->exists($layanan->gambar)) {
                        Storage::disk('public')->delete($layanan->gambar);
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

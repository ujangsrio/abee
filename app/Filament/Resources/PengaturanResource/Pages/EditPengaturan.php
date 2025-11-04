<?php

namespace App\Filament\Resources\PengaturanResource\Pages;

use App\Filament\Resources\PengaturanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Filament\Notifications\Notification;

class EditPengaturan extends EditRecord
{
    protected static string $resource = PengaturanResource::class;

    protected function getHeaderActions(): array
    {
        // Nonaktifkan delete, karena admin tidak boleh hapus akun sendiri
        return [];
    }

    /**
     * Mutasi data form sebelum disimpan ke database.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Kalau user isi new_password → hash dan simpan
        if (!empty($this->data['new_password'])) {
            $data['password'] = Hash::make($this->data['new_password']);
        } else {
            // Kalau tidak isi, jangan ubah kolom password
            unset($data['password']);
        }

        return $data;
    }

    /**
     * Setelah record berhasil diupdate
     */
    protected function afterSave(): void
    {
        // Hapus isi field password di form agar tidak tampil
        $this->form->fill([
            'current_password' => '',
            'new_password' => '',
            'new_password_confirmation' => '',
        ]);

        // Tampilkan notifikasi sukses
        Notification::make()
            ->title('Profil berhasil diperbarui')
            ->body('Informasi akun dan kata sandi Anda telah disimpan dengan aman.')
            ->success()
            ->send();
    }

    protected function rules(): array
    {
        return [
            'data.name' => ['required', 'string', 'min:5', 'max:50'],
            'data.email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->record->id), // boleh pakai email lama sendiri
            ],
            'data.current_password' => ['required'],
            'data.new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Ubah teks notifikasi default bawaan Filament
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Perubahan berhasil disimpan';
    }
}

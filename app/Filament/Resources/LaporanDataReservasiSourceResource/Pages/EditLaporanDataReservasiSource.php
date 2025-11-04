<?php

namespace App\Filament\Resources\LaporanDataReservasiSourceResource\Pages;

use App\Filament\Resources\LaporanDataReservasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanDataReservasiSource extends EditRecord
{
    protected static string $resource = LaporanDataReservasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\LaporanDataReservasiSourceResource\Pages;

use App\Filament\Resources\LaporanDataReservasiSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanDataReservasiSource extends EditRecord
{
    protected static string $resource = LaporanDataReservasiSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

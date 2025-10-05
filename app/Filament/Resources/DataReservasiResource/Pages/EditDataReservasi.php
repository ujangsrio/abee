<?php

namespace App\Filament\Resources\DataReservasiResource\Pages;

use App\Filament\Resources\DataReservasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDataReservasi extends EditRecord
{
    protected static string $resource = DataReservasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

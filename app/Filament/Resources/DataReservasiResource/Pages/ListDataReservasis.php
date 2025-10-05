<?php

namespace App\Filament\Resources\DataReservasiResource\Pages;

use App\Filament\Resources\DataReservasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataReservasis extends ListRecords
{
    protected static string $resource = DataReservasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\LaporanDataReservasiSourceResource\Pages;

use App\Filament\Resources\LaporanDataReservasiSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanDataReservasiSources extends ListRecords
{
    protected static string $resource = LaporanDataReservasiSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

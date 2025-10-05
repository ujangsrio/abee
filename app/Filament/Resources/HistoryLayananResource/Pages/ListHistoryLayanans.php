<?php

namespace App\Filament\Resources\HistoryLayananResource\Pages;

use App\Filament\Resources\HistoryLayananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHistoryLayanans extends ListRecords
{
    protected static string $resource = HistoryLayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

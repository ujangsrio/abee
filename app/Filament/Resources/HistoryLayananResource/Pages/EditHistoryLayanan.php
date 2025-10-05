<?php

namespace App\Filament\Resources\HistoryLayananResource\Pages;

use App\Filament\Resources\HistoryLayananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistoryLayanan extends EditRecord
{
    protected static string $resource = HistoryLayananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

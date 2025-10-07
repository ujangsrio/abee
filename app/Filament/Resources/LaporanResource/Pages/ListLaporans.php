<?php

namespace App\Filament\Resources\LaporanResource\Pages;

use App\Filament\Resources\LaporanResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Exports\LaporanExport;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Exports\LaporanPdf; 

class ListLaporans extends ListRecords
{
    protected static string $resource = LaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export ke Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (array $data, Request $request) {
                    $startDate = $this->tableFilters['date_range']['start_date'] ?? null;
                    $endDate = $this->tableFilters['date_range']['end_date'] ?? null;
                    $status = $this->tableFilters['status'] ?? null; 

                    $export = new LaporanExport($startDate, $endDate, $status);
                    return $export->download();
                }), 
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widget akan ditampilkan di custom view
        ];
    }

    // protected function getHeader(): ?string
    // {
    //     return 'Laporan Reservasi - Aretha Beauty';
    // }

    protected function getContent(): string
    {
        return view('filament.resources.laporan-resource.pages.list-laporans', [
            'rekapHarian' => LaporanResource::getRekapHarian(),
            'rekapMingguan' => LaporanResource::getRekapMingguan(),
            'rekapBulanan' => LaporanResource::getRekapBulanan(),
        ])->render();
    }
}

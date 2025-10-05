<?php

namespace App\Filament\Resources\LaporanResource\Pages;

use App\Filament\Resources\LaporanResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Exports\LaporanExport;
use Illuminate\Support\Carbon;
use App\Exports\LaporanPdf; 

class ListLaporans extends ListRecords
{
    protected static string $resource = LaporanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $export = new LaporanExport();
                    return $export->download();
                }),

            // // Tambahkan action ini di getHeaderActions()
            // Action::make('export_pdf')
            //     ->label('Export HTML/PDF')
            //     ->icon('heroicon-o-document')
            //     ->color('danger')
            //     ->action(function () {
            //         $export = new \App\Exports\LaporanPdf();
            //         return $export->download();
            //     }),
        
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

<?php

namespace App\Filament\Resources\LaporanDataReservasiSourceResource\Pages;



use App\Filament\Resources\LaporanDataReservasiResource;
use App\Exports\LaporanReservasiExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;

class ListLaporanDataReservasis extends ListRecords
{
    protected static string $resource = LaporanDataReservasiResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Dari Tanggal'),
                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('Sampai Tanggal'),
                ])
                ->action(function (array $data) {
                    $startDate = $data['start_date'];
                    $endDate = $data['end_date'];

                    try {
                        $export = new LaporanReservasiExport($startDate, $endDate);
                        $fileName = 'laporan-reservasi-' . Carbon::now()->format('Y-m-d-H-i') . '.xlsx';

                        return Excel::download($export, $fileName);
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Error Export')
                            ->danger()
                            ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                            ->send();
                    }
                })
                ->modalHeading('Export Laporan Reservasi')
                ->modalSubmitActionLabel('Download Excel'),

            Actions\Action::make('export_quick')
                ->label('Export Semua Data')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->action(function () {
                    try {
                        $export = new LaporanReservasiExport();
                        $fileName = 'laporan-reservasi-semua-' . Carbon::now()->format('Y-m-d-H-i') . '.xlsx';

                        return Excel::download($export, $fileName);
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Error Export')
                            ->danger()
                            ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                            ->send();
                    }
                }),
        ];
    }
}

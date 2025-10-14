<?php

namespace App\Filament\Resources\LaporanKeuanganResource\Pages;

use App\Filament\Resources\LaporanKeuanganResource;
use App\Models\LabaRugi;
use App\Exports\LaporanKeuanganExport;
use App\Exports\RingkasanKeuanganExport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ListLaporanKeuangans extends ListRecords
{
    protected static string $resource = LaporanKeuanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Data')
                ->icon('heroicon-o-plus'),

            // Export Action dengan Modal Form
            Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('jenis_laporan')
                        ->label('Jenis Laporan')
                        ->options([
                            'detail' => 'Detail Lengkap',
                            'ringkasan' => 'Ringkasan Keuangan',
                        ])
                        ->required()
                        ->default('detail'),
                    \Filament\Forms\Components\DatePicker::make('start_date')
                        ->label('Dari Tanggal'),
                    \Filament\Forms\Components\DatePicker::make('end_date')
                        ->label('Sampai Tanggal'),
                ])
                ->action(function (array $data) {
                    $jenis = $data['jenis_laporan'];
                    $startDate = $data['start_date'];
                    $endDate = $data['end_date'];

                    try {
                        if ($jenis === 'detail') {
                            $export = new LaporanKeuanganExport($startDate, $endDate);
                            $fileName = 'laporan-keuangan-detail-' . Carbon::now()->format('Y-m-d-H-i') . '.xlsx';
                        } else {
                            $export = new RingkasanKeuanganExport($startDate, $endDate);
                            $fileName = 'ringkasan-keuangan-' . Carbon::now()->format('Y-m-d-H-i') . '.xlsx';
                        }

                        return Excel::download($export, $fileName);
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error Export')
                            ->danger()
                            ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                            ->send();
                    }
                })
                ->modalHeading('Export Laporan Keuangan')
                ->modalSubmitActionLabel('Download Excel'),

            Actions\Action::make('ringkasan')
                ->label('Lihat Ringkasan')
                ->icon('heroicon-o-chart-bar')
                ->color('success')
                ->action(function () {
                    $ringkasan = LaporanKeuanganResource::getRingkasanKeuangan();

                    Notification::make()
                        ->title('📊 Ringkasan Keuangan')
                        ->success()
                        ->body(
                            "Pendapatan Reservasi: Rp " . number_format($ringkasan['pendapatan_reservasi'], 0, ',', '.') . "\n" .
                                "Pendapatan Manual: Rp " . number_format($ringkasan['pendapatan_manual'], 0, ',', '.') . "\n" .
                                "Total Pendapatan: Rp " . number_format($ringkasan['total_pendapatan'], 0, ',', '.') . "\n" .
                                "Total Pengeluaran: Rp " . number_format($ringkasan['total_pengeluaran'], 0, ',', '.') . "\n" .
                                "Laba Bersih: Rp " . number_format($ringkasan['laba_bersih'], 0, ',', '.')
                        )
                        ->send();
                }),
        ];
    }

    // Hapus getHeaderWidgets() untuk sementara untuk menghindari error
    // protected function getHeaderWidgets(): array
    // {
    //     return [];
    // }
}

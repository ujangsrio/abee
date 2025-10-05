<?php

namespace App\Filament\Resources;

use App\Models\CustomerBooking;
use App\Models\Layanan;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use App\Filament\Resources\LaporanResource\Pages;

class LaporanResource extends Resource
{
    protected static ?string $model = CustomerBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan';

    protected static ?string $modelLabel = 'Laporan';

    protected static ?string $pluralModelLabel = 'Laporan';

    protected static ?int $navigationSort = 1;

    // Nonaktifkan create, edit, delete
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('layanan_dipesan')
                    ->label('Layanan')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return \App\Helpers\LayananHelper::getNamaLayanan($record->service_id);
                    }),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('time')
                    ->label('Waktu')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Dikonfirmasi' => 'info',
                        'Selesai' => 'success',
                        'Dibatalkan' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('harga_layanan')
                    ->label('Harga')
                    ->money('IDR')
                    ->getStateUsing(function ($record) {
                        $layanan = Layanan::find($record->service_id);
                        return $layanan ? $layanan->harga : 0;
                    })
                    ->alignRight(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Reservasi')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Dikonfirmasi' => 'Dikonfirmasi',
                        'Selesai' => 'Selesai',
                        'Dibatalkan' => 'Dibatalkan',
                    ]),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('start_date')
                            ->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('end_date')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_date'], fn($q) => $q->whereDate('date', '>=', $data['start_date']))
                            ->when($data['end_date'], fn($q) => $q->whereDate('date', '<=', $data['end_date']));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->modalHeading('Detail Reservasi')
                    ->modalContent(function (CustomerBooking $record) {
                        $layanan = Layanan::find($record->service_id);

                        return view('filament.resources.laporan-resources.modals.view-details', [
                            'booking' => $record,
                            'layanan' => $layanan,
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->bulkActions([
                // Nonaktifkan bulk actions
            ])
            ->emptyStateHeading('Belum ada data reservasi')
            ->emptyStateDescription('Data reservasi akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-calendar')
            ->defaultSort('date', 'desc')
            ->deferLoading();
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with(['service']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporans::route('/'),
        ];
    }

    // Method untuk menghitung rekap
    public static function getRekapHarian(): array
    {
        $today = Carbon::today();
        $bookings = CustomerBooking::whereDate('date', $today)->get();

        return self::calculateRekap($bookings, $today->format('d M Y'));
    }

    public static function getRekapMingguan(): array
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $bookings = CustomerBooking::whereBetween('date', [$weekStart, $weekEnd])->get();

        $periode = $weekStart->format('d M') . ' - ' . $weekEnd->format('d M Y');

        return self::calculateRekap($bookings, $periode);
    }

    public static function getRekapBulanan(): array
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $bookings = CustomerBooking::whereBetween('date', [$monthStart, $monthEnd])->get();

        $periode = $monthStart->format('F Y');

        return self::calculateRekap($bookings, $periode);
    }

    private static function calculateRekap($bookings, $periode): array
    {
        $total = $bookings->count();
        $selesai = $bookings->where('status', 'Selesai')->count();
        $dibatalkan = $bookings->where('status', 'Dibatalkan')->count();

        // Hitung total pendapatan dari booking yang selesai menggunakan helper
        $completedBookings = $bookings->where('status', 'Selesai');
        $totalPendapatan = 0;

        foreach ($completedBookings as $booking) {
            $totalPendapatan += \App\Helpers\LayananHelper::getHargaLayanan($booking->service_id);
        }

        return [
            'periode' => $periode,
            'total_reservasi' => $total,
            'selesai' => $selesai,
            'dibatalkan' => $dibatalkan,
            'total_pendapatan' => $totalPendapatan,
        ];
    }
}

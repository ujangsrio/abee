<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanDataReservasiSourceResource\Pages\ListLaporanDataReservasis;
// use App\Filament\Resources\LaporanDataReservasiResource\Pages\ListLaporanDataReservasis;
use App\Models\CustomerBooking;
use App\Models\Layanan;
use App\Exports\LaporanReservasiExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;

class LaporanDataReservasiResource extends Resource
{
    protected static ?string $model = CustomerBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Reservasi';

    protected static ?string $modelLabel = 'Laporan Reservasi';

    protected static ?string $pluralModelLabel = 'Laporan Reservasi';

    protected static ?string $navigationGroup = 'Laporan';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form tidak diperlukan untuk laporan
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                CustomerBooking::query()->where('status', 'Selesai')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer.whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('service.nama')
                    ->label('Layanan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->formatStateUsing(function ($state) {
                        $tipeLayanan = $state;

                        if (is_array($tipeLayanan)) {
                            return collect($tipeLayanan)->map(function ($item) {
                                return match ($item) {
                                    'home_service' => 'Home Service',
                                    'studio' => 'Studio',
                                    default => ucfirst($item)
                                };
                            })->implode(', ');
                        }

                        if (is_string($tipeLayanan)) {
                            try {
                                $decoded = json_decode($tipeLayanan, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    return collect($decoded)->map(function ($item) {
                                        return match ($item) {
                                            'home_service' => 'Home Service',
                                            'studio' => 'Studio',
                                            default => ucfirst($item)
                                        };
                                    })->implode(', ');
                                }
                            } catch (\Exception $e) {
                                // Jika gagal decode
                            }
                        }

                        if (is_string($tipeLayanan)) {
                            return match ($tipeLayanan) {
                                'home_service' => 'Home Service',
                                'studio' => 'Studio',
                                default => ucfirst($tipeLayanan)
                            };
                        }

                        return '-';
                    })
                    ->badge()
                    ->color(function ($state) {
                        if (str_contains($state, 'Home Service')) {
                            return 'success';
                        } elseif (str_contains($state, 'Studio')) {
                            return 'primary';
                        }
                        return 'gray';
                    }),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d-m-Y') // FORMAT DIPERBAIKI
                    ->sortable(),

                Tables\Columns\TextColumn::make('time')
                    ->label('Jam')
                    ->sortable(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->service->harga ?? 0;
                    })
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total Pendapatan')
                            ->query(function ($query) {
                                return $query->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
                                    ->select(DB::raw('SUM(layanans.harga) as total'));
                            }),
                    ]),

                Tables\Columns\TextColumn::make('tipe_pembayaran')
                    ->label('Tipe Bayar')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'full' => 'success',
                        'dp' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'full' => 'Lunas',
                        'dp' => 'DP',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status_dp')
                    ->label('Status DP')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status Reservasi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Dikonfirmasi' => 'success',
                        'Dibatalkan' => 'danger',
                        'Selesai' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Reservasi')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Dikonfirmasi' => 'Dikonfirmasi',
                        'Dibatalkan' => 'Dibatalkan',
                        'Selesai' => 'Selesai',
                    ])
                    ->default('Selesai'),

                SelectFilter::make('status_dp')
                    ->label('Status Pembayaran')
                    ->options([
                        'Belum' => 'Belum',
                        'Lunas' => 'Lunas',
                    ]),

                SelectFilter::make('tipe_pembayaran')
                    ->label('Tipe Pembayaran')
                    ->options([
                        'dp' => 'DP',
                        'full' => 'Lunas',
                    ]),

                SelectFilter::make('service_id')
                    ->label('Layanan')
                    ->relationship('service', 'nama'),

                Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal')
                            ->displayFormat('d-m-Y'), // FORMAT DIPERBAIKI
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Sampai Tanggal')
                            ->displayFormat('d-m-Y'), // FORMAT DIPERBAIKI
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['date_from'] ?? null) {
                            $indicators['date_from'] = 'Dari: ' . Carbon::parse($data['date_from'])->format('d-m-Y');
                        }

                        if ($data['date_until'] ?? null) {
                            $indicators['date_until'] = 'Sampai: ' . Carbon::parse($data['date_until'])->format('d-m-Y');
                        }

                        return $indicators;
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (array $data = []) {
                        $dateFrom = $data['date_from'] ?? null;
                        $dateUntil = $data['date_until'] ?? null;
                        $status = $data['status'] ?? null;
                        $serviceId = $data['service_id'] ?? null;

                        $fileName = 'laporan-reservasi-' . now()->format('d-m-Y') . '.xlsx';

                        return Excel::download(
                            new LaporanReservasiExport($dateFrom, $dateUntil, $status, $serviceId),
                            $fileName
                        );
                    })
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal')
                            ->displayFormat('d-m-Y'), // FORMAT DIPERBAIKI
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Sampai Tanggal')
                            ->displayFormat('d-m-Y'), // FORMAT DIPERBAIKI
                        Forms\Components\Select::make('status')
                            ->label('Status Reservasi')
                            ->options([
                                'Menunggu' => 'Menunggu',
                                'Dikonfirmasi' => 'Dikonfirmasi',
                                'Dibatalkan' => 'Dibatalkan',
                                'Selesai' => 'Selesai',
                            ]),
                        Forms\Components\Select::make('service_id')
                            ->label('Layanan')
                            ->relationship('service', 'nama'),
                    ])
                    ->modalHeading('Export Laporan Reservasi')
                    ->modalSubmitActionLabel('Export')
                    ->modalWidth('md'),
            ])
            ->emptyStateHeading('Belum ada data laporan')
            ->emptyStateDescription('Data laporan akan muncul di sini ketika ada reservasi yang selesai.')
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->defaultSort('date', 'desc')
            ->deferLoading()
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaporanDataReservasis::route('/'), // ← PASTIKAN INI
        ];
    }
}

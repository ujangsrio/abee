<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanDataReservasiResource\Pages;
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

                Tables\Columns\TagsColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->getStateUsing(function ($record) {
                        $tipeLayanan = $record->tipe_layanan;

                        if (is_array($tipeLayanan)) {
                            return collect($tipeLayanan)->map(function ($item) {
                                return match ($item) {
                                    'home_service' => 'Home Service',
                                    'studio' => 'Studio',
                                    default => ucfirst($item)
                                };
                            })->toArray();
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
                                    })->toArray();
                                }
                            } catch (\Exception $e) {
                                // Jika gagal decode
                            }
                        }

                        if (is_string($tipeLayanan)) {
                            return [match ($tipeLayanan) {
                                'home_service' => 'Home Service',
                                'studio' => 'Studio',
                                default => ucfirst($tipeLayanan)
                            }];
                        }

                        return ['-'];
                    })
                    ->separator(',')
                    ->colors([
                        'primary' => 'Studio',
                        'success' => 'Home Service',
                    ]),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('time')
                    ->label('Jam')
                    ->sortable(),

                // SOLUSI: Gunakan approach yang lebih aman tanpa inverse relationship
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
                                    ->select(DB::raw('SUM(layanans.harga) as total'))
                                    ->where('customer_bookings.status', 'Selesai');
                            })
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

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter Status (hanya untuk konfirmasi, seharusnya sudah Selesai)
                SelectFilter::make('status')
                    ->label('Status Reservasi')
                    ->options([
                        'Selesai' => 'Selesai',
                    ])
                    ->default('Selesai')
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'Selesai') {
                            $query->where('status', 'Selesai');
                        }
                    }),

                // Filter Tanggal
                Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_dari')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('tanggal_sampai')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tanggal_dari'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['tanggal_sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),

                // Filter Bulan
                Filter::make('bulan')
                    ->form([
                        Forms\Components\Select::make('bulan')
                            ->label('Bulan')
                            ->options([
                                '01' => 'Januari',
                                '02' => 'Februari',
                                '03' => 'Maret',
                                '04' => 'April',
                                '05' => 'Mei',
                                '06' => 'Juni',
                                '07' => 'Juli',
                                '08' => 'Agustus',
                                '09' => 'September',
                                '10' => 'Oktober',
                                '11' => 'November',
                                '12' => 'Desember',
                            ])
                            ->default(now()->format('m')),
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $years = [];
                                $startYear = 2024;
                                $currentYear = now()->year;

                                for ($year = $startYear; $year <= $currentYear; $year++) {
                                    $years[$year] = $year;
                                }

                                return $years;
                            })
                            ->default(now()->format('Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['bulan'] && $data['tahun'],
                                function (Builder $query) use ($data) {
                                    $startDate = Carbon::create($data['tahun'], $data['bulan'], 1)->startOfMonth();
                                    $endDate = Carbon::create($data['tahun'], $data['bulan'], 1)->endOfMonth();

                                    return $query->whereBetween('date', [$startDate, $endDate]);
                                }
                            );
                    }),

                // Filter Tahun
                Filter::make('tahun')
                    ->form([
                        Forms\Components\Select::make('tahun')
                            ->label('Tahun')
                            ->options(function () {
                                $years = [];
                                $startYear = 2024;
                                $currentYear = now()->year;

                                for ($year = $startYear; $year <= $currentYear; $year++) {
                                    $years[$year] = $year;
                                }

                                return $years;
                            })
                            ->default(now()->format('Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['tahun'],
                                function (Builder $query) use ($data) {
                                    $startDate = Carbon::create($data['tahun'], 1, 1)->startOfYear();
                                    $endDate = Carbon::create($data['tahun'], 12, 31)->endOfYear();

                                    return $query->whereBetween('date', [$startDate, $endDate]);
                                }
                            );
                    }),

                // Filter Hari Ini
                Filter::make('hari_ini')
                    ->label('Hari Ini')
                    ->query(fn(Builder $query): Builder => $query->whereDate('date', today())),

                // Filter 7 Hari Terakhir
                Filter::make('7_hari_terakhir')
                    ->label('7 Hari Terakhir')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('date', [today()->subDays(6), today()])),

                // Filter 30 Hari Terakhir
                Filter::make('30_hari_terakhir')
                    ->label('30 Hari Terakhir')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('date', [today()->subDays(29), today()])),

                // Filter Layanan
                SelectFilter::make('service_id')
                    ->label('Layanan')
                    ->relationship('service', 'nama')
                    ->searchable()
                    ->preload(),

                // Filter Tipe Layanan
                SelectFilter::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->options([
                        'studio' => 'Studio',
                        'home_service' => 'Home Service',
                    ])
                    ->multiple()
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['values'])) {
                            $query->where(function ($q) use ($data) {
                                foreach ($data['values'] as $tipe) {
                                    $q->orWhereJsonContains('tipe_layanan', $tipe);
                                }
                            });
                        }
                    }),

                // Filter Tipe Pembayaran
                SelectFilter::make('tipe_pembayaran')
                    ->label('Tipe Pembayaran')
                    ->options([
                        'dp' => 'DP',
                        'full' => 'Lunas',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tidak ada bulk actions untuk laporan
            ])
            ->defaultSort('date', 'desc')
            ->emptyStateHeading('Tidak ada data laporan')
            ->emptyStateDescription('Belum ada reservasi yang selesai dalam periode yang dipilih.')
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->deferFilters();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanDataReservasis::route('/'),
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'Selesai')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}

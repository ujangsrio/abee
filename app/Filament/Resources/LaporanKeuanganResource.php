<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanKeuanganResource\Pages;
use App\Models\CustomerBooking;
use App\Models\LabaRugi;
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

class LaporanKeuanganResource extends Resource
{
    protected static ?string $model = LabaRugi::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Laporan Keuangan';

    protected static ?string $modelLabel = 'Laporan Keuangan';

    protected static ?string $pluralModelLabel = 'Laporan Keuangan';

    protected static ?string $navigationGroup = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Keuangan')
                    ->schema([
                        Forms\Components\Select::make('kategori')
                            ->label('Kategori')
                            ->options([
                                'Pendapatan' => 'Pendapatan',
                                'Pengeluaran' => 'Pengeluaran',
                            ])
                            ->required()
                            ->reactive(),
                        Forms\Components\TextInput::make('nama_item')
                            ->label('Nama Item')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('jumlah')
                            ->label('Jumlah (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0),
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pendapatan' => 'success',
                        'Pengeluaran' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_item')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn($record) => $record->kategori === 'Pendapatan' ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->keterangan;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter Periode
                Filter::make('periode')
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
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['tanggal_sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
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

                                    return $query->whereBetween('tanggal', [$startDate, $endDate]);
                                }
                            );
                    }),

                // Filter Kategori
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'Pendapatan' => 'Pendapatan',
                        'Pengeluaran' => 'Pengeluaran',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada data laba rugi')
            ->emptyStateDescription('Mulai tambahkan data pendapatan dan pengeluaran untuk melihat laporan keuangan.')
            ->emptyStateIcon('heroicon-o-banknotes')
            ->deferFilters();
    }

    public static function getRelations(): array
    {
        return [
            // Relation untuk ringkasan keuangan
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanKeuangans::route('/'),
            'create' => Pages\CreateLaporanKeuangan::route('/create'),
            'edit' => Pages\EditLaporanKeuangan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    /**
     * Method untuk menghitung total pendapatan dari reservasi
     */
    public static function getTotalPendapatanReservasi($startDate = null, $endDate = null): float
    {
        $query = CustomerBooking::where('status', 'Selesai');

        if ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        }

        return $query->join('layanans', 'customer_bookings.service_id', '=', 'layanans.id')
            ->sum('layanans.harga');
    }

    /**
     * Method untuk menghitung ringkasan keuangan
     */
    public static function getRingkasanKeuangan($startDate = null, $endDate = null): array
    {
        // Total pendapatan dari reservasi
        $pendapatanReservasi = self::getTotalPendapatanReservasi($startDate, $endDate);

        // Query untuk data laba rugi manual
        $labaRugiQuery = LabaRugi::query();

        if ($startDate) {
            $labaRugiQuery->whereDate('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $labaRugiQuery->whereDate('tanggal', '<=', $endDate);
        }

        $labaRugiData = $labaRugiQuery->get();

        // Hitung total pendapatan dan pengeluaran manual
        $pendapatanManual = $labaRugiData->where('kategori', 'Pendapatan')->sum('jumlah');
        $pengeluaranManual = $labaRugiData->where('kategori', 'Pengeluaran')->sum('jumlah');

        // Total keseluruhan
        $totalPendapatan = $pendapatanReservasi + $pendapatanManual;
        $totalPengeluaran = $pengeluaranManual;
        $labaBersih = $totalPendapatan - $totalPengeluaran;

        return [
            'pendapatan_reservasi' => $pendapatanReservasi,
            'pendapatan_manual' => $pendapatanManual,
            'total_pendapatan' => $totalPendapatan,
            'total_pengeluaran' => $totalPengeluaran,
            'laba_bersih' => $labaBersih,
            'periode' => [
                'start' => $startDate,
                'end' => $endDate,
            ]
        ];
    }
}

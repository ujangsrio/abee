<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoResource\Pages;
use App\Models\Promo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class PromoResource extends Resource
{
    protected static ?string $model = Promo::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    protected static ?string $navigationLabel = 'Manajemen Promo';
    protected static ?string $modelLabel = 'Promo';
    protected static ?string $pluralModelLabel = 'Manajemen Promo';
    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Promo')
                    ->schema([
                        Forms\Components\TextInput::make('nama_promo')
                            ->label('Nama Promo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Promo Bulan Ini, Diskon Spesial, dll.')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Promo')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Jelaskan detail promo yang akan ditawarkan...')
                            ->helperText('Deskripsi akan ditampilkan kepada pelanggan')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('diskon')
                            ->label('Diskon (%)')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->step(1)
                            ->suffix('%')
                            ->placeholder('10')
                            ->helperText('Masukkan persentase diskon dari 1% hingga 100%')
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('hanya_member')
                            ->label('Hanya untuk Member?')
                            ->default(false)
                            ->inline(false)
                            ->helperText('Jika diaktifkan, promo hanya bisa digunakan oleh member')
                            ->columnSpan(1),

                        Forms\Components\DatePicker::make('tanggal_berakhir')
                            ->label('Tanggal Berakhir Promo')
                            ->required()
                            ->minDate(now()->format('Y-m-d'))
                            ->default(now()->addMonth()->format('Y-m-d'))
                            ->helperText('Promo akan aktif sampai tanggal ini')
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('info_status')
                            ->label('Status Promo')
                            ->content(function (Forms\Get $get) {
                                $tanggalBerakhir = $get('tanggal_berakhir');
                                if ($tanggalBerakhir) {
                                    $now = now();
                                    $berakhir = Carbon::parse($tanggalBerakhir);

                                    if ($berakhir->isPast()) {
                                        return '❌ Promo telah berakhir';
                                    } elseif ($berakhir->isToday()) {
                                        return '⚠️ Promo berakhir hari ini';
                                    } else {
                                        $sisaHari = $now->diffInDays($berakhir, false);
                                        if ($sisaHari > 0) {
                                            return "✅ Aktif - {$sisaHari} hari lagi";
                                        }
                                    }
                                }
                                return '📝 Belum diatur';
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_promo')
                    ->label('Nama Promo')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Promo $record): string => $record->deskripsi ?: '-')
                    ->wrap(),

                Tables\Columns\TextColumn::make('diskon')
                    ->label('Diskon')
                    ->formatStateUsing(fn($state) => "{$state}%")
                    ->color('success')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('tanggal_berakhir')
                    ->label('Berakhir Pada')
                    ->date('d M Y')
                    ->sortable()
                    ->color(function (Promo $record) {
                        if ($record->tanggal_berakhir < now()) {
                            return 'danger';
                        } elseif ($record->tanggal_berakhir->isToday()) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->description(function (Promo $record) {
                        if ($record->tanggal_berakhir >= now()) {
                            $sisaHari = now()->diffInDays($record->tanggal_berakhir, false);
                            if ($sisaHari > 0) {
                                return "{$sisaHari} hari lagi";
                            } elseif ($sisaHari == 0) {
                                return 'Hari ini';
                            }
                        }
                        return 'Berakhir';
                    }),

                Tables\Columns\IconColumn::make('hanya_member')
                    ->label('Member Only')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (Promo $record) {
                        if ($record->tanggal_berakhir < now()) {
                            return 'Kadaluarsa';
                        } elseif ($record->tanggal_berakhir->isToday()) {
                            return 'Berakhir Hari Ini';
                        } else {
                            return 'Aktif';
                        }
                    })
                    ->color(function (Promo $record) {
                        if ($record->tanggal_berakhir < now()) {
                            return 'danger';
                        } elseif ($record->tanggal_berakhir->isToday()) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('aktif')
                    ->label('Promo Aktif')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('tanggal_berakhir', '>=', now()->format('Y-m-d'))
                    ),

                Tables\Filters\Filter::make('kadaluarsa')
                    ->label('Promo Kadaluarsa')
                    ->query(
                        fn(Builder $query): Builder =>
                        $query->where('tanggal_berakhir', '<', now()->format('Y-m-d'))
                    ),

                Tables\Filters\TernaryFilter::make('hanya_member')
                    ->label('Hanya untuk Member')
                    ->trueLabel('Ya')
                    ->falseLabel('Tidak'),

                Tables\Filters\Filter::make('diskon_range')
                    ->label('Range Diskon')
                    ->form([
                        Forms\Components\TextInput::make('diskon_min')
                            ->label('Diskon Min (%)')
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('diskon_max')
                            ->label('Diskon Max (%)')
                            ->numeric()
                            ->maxValue(100),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['diskon_min'],
                                fn(Builder $query, $diskon): Builder =>
                                $query->where('diskon', '>=', $diskon)
                            )
                            ->when(
                                $data['diskon_max'],
                                fn(Builder $query, $diskon): Builder =>
                                $query->where('diskon', '<=', $diskon)
                            );
                    }),

                // Filter untuk diskon tertentu
                Tables\Filters\SelectFilter::make('diskon_pilihan')
                    ->label('Diskon Pilihan')
                    ->options([
                        10 => '10%',
                        15 => '15%',
                        20 => '20%',
                        25 => '25%',
                        30 => '30%',
                        50 => '50%',
                    ])
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton(),

                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPromos::route('/'),
            'create' => Pages\CreatePromo::route('/create'),
            'edit' => Pages\EditPromo::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('tanggal_berakhir', '>=', now()->format('Y-m-d'))->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}

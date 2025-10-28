<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayananResource\Pages;
use App\Models\Layanan;
use App\Models\Promo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
// PERBAIKAN: Gunakan Illuminate\Support\Carbon untuk memastikan fungsi now() dikenali.
use Illuminate\Support\Carbon; 

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Manajemen Layanan';
    protected static ?string $modelLabel = 'Layanan';
    protected static ?string $pluralModelLabel = 'Manajemen Layanan';

    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Layanan')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Layanan')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->maxLength(255)
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('harga')
                        ->label('Harga (Rp)')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->columnSpan(1),

                    Forms\Components\Select::make('promo_id')
                        ->label('Pilih Promo (Opsional)')
                        // Pastikan relasi 'promo' sudah ada di App\Models\Layanan
                        ->relationship(
                            'promo', // 1. Nama Relasi Eloquent
                            'nama_promo', // 2. Atribut Judul yang Ditampilkan
                            // 3. Query Modifier Closure (Memfilter Promo Aktif)
                            fn (Builder $query) => $query->whereDate('tanggal_berakhir', '>=', now())
                        )
                        ->placeholder('Tidak ada promo yang diterapkan')
                        ->nullable()
                        ->searchable()
                        ->preload()
                        ->helperText('Pilih promo aktif yang ingin diterapkan pada layanan ini. Kosongkan jika tidak ada promo.')
                        ->columnSpan(1),

                    Forms\Components\FileUpload::make('gambar')
                        ->label('Gambar Layanan')
                        ->image()
                        ->directory('photos')
                        ->maxSize(2048)
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('16:9')
                        ->imageResizeTargetWidth('800')
                        ->imageResizeTargetHeight('450')
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Tipe Layanan')
                ->schema([
                    Forms\Components\CheckboxList::make('tipe_layanan')
                        ->label('Tipe Layanan yang Tersedia')
                        ->options([
                            'studio' => 'Studio',
                            'home_service' => 'Home Service',
                        ])
                        ->default(['studio', 'home_service'])
                        ->columns(2)
                        ->helperText('Pilih tipe layanan yang tersedia untuk layanan ini')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Jadwal Layanan')
                ->description('Atur tanggal dan jam utama untuk layanan ini')
                ->schema([
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Tersedia')
                        ->required()
                        ->default(now()->format('Y-m-d'))
                        ->minDate(today())
                        ->helperText('Pilih tanggal ketika layanan ini tersedia')
                        ->columnSpan(1),

                    Forms\Components\TimePicker::make('jam')
                        ->label('Jam Utama')
                        ->seconds(false)
                        ->default(now()->format('H:i'))
                        ->helperText('Jam utama untuk layanan ini')
                        ->columnSpan(1),
                ])
                ->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Layanan')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Layanan $record): string => $record->deskripsi ?: '-')
                    ->wrap(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),

                // Menampilkan Nama Promo
                Tables\Columns\TextColumn::make('promo.nama_promo')
                    ->label('Promo Diterapkan')
                    ->badge()
                    ->color('warning')
                    ->placeholder('Tidak Ada Promo')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d-m-y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('jam')
                    ->label('Jam')
                    ->time('H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TagsColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->getStateUsing(function ($record) {
                        if (!$record->tipe_layanan) {
                            return [];
                        }

                        // Menggunakan array_map dan null coalescing operator untuk keamanan
                        return array_map(function ($type) {
                            return match ($type) {
                                'studio' => 'Studio',
                                'home_service' => 'Home Service',
                                default => ucfirst($type),
                            };
                        }, (array) $record->tipe_layanan);
                    })
                    ->toggleable(),
                
                // Kolom 'total_dipesan' telah dihapus sesuai permintaan.
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d-m-y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->options([
                        'studio' => 'Studio',
                        'home_service' => 'Home Service',
                    ])
                    ->multiple(),

                // Filter Promo
                Tables\Filters\TernaryFilter::make('promo_id')
                    ->label('Status Promo')
                    ->nullable()
                    ->placeholder('Semua Layanan')
                    ->trueLabel('Ada Promo Diterapkan')
                    ->falseLabel('Tidak Ada Promo')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('promo_id'),
                        false: fn (Builder $query) => $query->whereNull('promo_id'),
                    ),

                Tables\Filters\Filter::make('tanggal_berlaku')
                    ->label('Tanggal Tersedia')
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

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayanans::route('/'),
            'create' => Pages\CreateLayanan::route('/create'),
            'edit' => Pages\EditLayanan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
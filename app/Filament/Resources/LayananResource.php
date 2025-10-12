<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayananResource\Pages;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'Manajemen Layanan';

    protected static ?string $modelLabel = 'Layanan';

    protected static ?string $pluralModelLabel = 'Manajemen Layanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Layanan')
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
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Pengaturan Layanan')
                    ->schema([
                        Forms\Components\CheckboxList::make('tipe_layanan')
                            ->label('Tipe Layanan')
                            ->options([
                                'studio' => 'Studio',
                                'home_service' => 'Home Service',
                            ])
                            ->default(['studio', 'home_service'])
                            ->columns(2)
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_promo')
                            ->label('Promo Aktif?')
                            ->default(false)
                            ->columnSpanFull(),

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
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Slot Waktu')
                    ->description('Atur slot waktu yang tersedia untuk layanan ini')
                    ->schema([
                        Forms\Components\Repeater::make('slots')
                            ->label('Daftar Slot')
                            ->relationship('slots')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DatePicker::make('tanggal')
                                            ->label('Tanggal Slot')
                                            ->required()
                                            ->default(now()->format('Y-m-d')),

                                        Forms\Components\TimePicker::make('jam')
                                            ->label('Jam Slot')
                                            ->required()
                                            ->seconds(false)
                                            ->default(now()->format('H:i')),
                                    ]),
                            ])
                            ->defaultItems(0)
                            ->collapsible()
                            ->reorderable()
                            ->addActionLabel('Tambah Slot')
                            ->minItems(0)
                            ->itemLabel(
                                fn(array $state): ?string =>
                                $state['tanggal'] && $state['jam']
                                    ? "Slot: {$state['tanggal']} {$state['jam']}"
                                    : null
                            ),
                    ])
                    ->collapsible()
                    ->collapsed(fn($operation) => $operation === 'edit'),

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

                Tables\Columns\TagsColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->getStateUsing(function ($record) {
                        if (!$record->tipe_layanan) {
                            return [];
                        }

                        return collect($record->tipe_layanan)->map(function ($type) {
                            return match ($type) {
                                'studio' => 'Studio',
                                'home_service' => 'Home Service',
                                default => ucfirst($type),
                            };
                        })->toArray();
                    })
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_promo')
                    ->label('Promo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-mark')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),

                // Kolom untuk menampilkan tanggal dan jam yang tersedia dari tabel slots
                Tables\Columns\TextColumn::make('slots.tanggal')
                    ->label('Tanggal Tersedia')
                    ->formatStateUsing(function (Layanan $record) {
                        $slots = $record->slots;
                        if ($slots->isEmpty()) {
                            return 'Tidak ada slot';
                        }

                        // Group slots by date
                        $groupedSlots = $slots->groupBy('tanggal');

                        $result = [];
                        foreach ($groupedSlots as $date => $dateSlots) {
                            $times = $dateSlots->pluck('jam')->map(function ($time) {
                                return Carbon::parse($time)->format('H:i');
                            })->sort()->implode(', ');

                            $formattedDate = Carbon::parse($date)->format('d M Y');
                            $result[] = "{$formattedDate} ({$times})";
                        }

                        return implode('; ', $result);
                    })
                    ->wrap()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('slots_count')
                    ->label('Jumlah Slot')
                    ->counts('slots')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal Berlaku')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
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

                Tables\Filters\TernaryFilter::make('is_promo')
                    ->label('Status Promo'),

                // Tables\Filters\Filter::make('tanggal_berlaku')
                //     ->form([
                //         Forms\Components\DatePicker::make('tanggal_dari')
                //             ->label('Dari Tanggal'),
                //         Forms\Components\DatePicker::make('tanggal_sampai')
                //             ->label('Sampai Tanggal'),
                //     ])
                //     ->query(function (Builder $query, array $data): Builder {
                //         return $query
                //             ->when(
                //                 $data['tanggal_dari'],
                //                 fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                //             )
                //             ->when(
                //                 $data['tanggal_sampai'],
                //                 fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                //             );
                //     }),

                // Filter berdasarkan slot yang tersedia
                Tables\Filters\Filter::make('has_slots')
                    ->label('Memiliki Slot')
                    ->query(fn(Builder $query): Builder => $query->has('slots')),

                Tables\Filters\Filter::make('no_slots')
                    ->label('Tidak Ada Slot')
                    ->query(fn(Builder $query): Builder => $query->doesntHave('slots')),
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
            // Tidak ada relations untuk sekarang
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayanans::route('/'),
            'create' => Pages\CreateLayanan::route('/create'),
            'edit' => Pages\EditLayanan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

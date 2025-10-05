<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayananResource\Pages;
use App\Filament\Resources\LayananResource\RelationManagers;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Layanan')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('harga')
                    ->label('Harga (Rp)')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),

                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()->format('Y-m-d')),

                Forms\Components\TimePicker::make('jam')
                    ->label('Jam')
                    ->required()
                    ->seconds(false)
                    ->default(now()->format('H:i')),

                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->maxLength(255)
                    ->rows(3),

                Forms\Components\FileUpload::make('gambar')
                    ->label('Gambar')
                    ->image()
                    ->directory('photos')
                    ->maxSize(2048),

                Forms\Components\Select::make('promo_id')
                    ->label('Promo')
                    ->relationship('promo', 'nama_promo')
                    ->searchable()
                    ->preload(),

                Forms\Components\CheckboxList::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->options([
                        'studio' => 'Studio',
                        'home_service' => 'Home Service',
                    ])
                    ->columns(2),

                
                Forms\Components\Section::make('Slot')
                    ->schema([
                        Forms\Components\Repeater::make('slots')
                            ->label('Daftar Slot')
                            ->relationship('slots')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\DatePicker::make('tanggal')
                                            ->label('Tanggal Slot')
                                            ->required()
                                            ->default(now()->format('Y-m-d')),
                                    ]),
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TimePicker::make('jam')
                                            ->label('Jam Slot')
                                            ->required()
                                            ->seconds(false)
                                            ->default(now()->format('H:i')),
                                    ]),
                            ])
                            ->columns(1)
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
                    ->collapsible(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->square(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TagsColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->getStateUsing(function ($record) {
                        if (!$record->tipe_layanan) {
                            return [];
                        }
                        return collect($record->tipe_layanan)->map(fn($type) => match ($type) {
                            'studio' => 'Studio',
                            'home_service' => 'Home Service',
                            default => $type,
                        })->toArray();
                    }),

                Tables\Columns\TextColumn::make('promo.nama_promo')
                    ->label('Promo')
                    ->badge()
                    ->color('success')
                    ->default('-'),

                Tables\Columns\TextColumn::make('slots')
                    ->label('Slot Tersedia')
                    ->html()
                    ->getStateUsing(function ($record) {
                        if ($record->slots->isEmpty()) {
                            return '<span class="text-gray-500 italic">Tidak ada</span>';
                        }
                        return $record->slots->map(function ($slot) {
                            return \Carbon\Carbon::parse($slot->tanggal)->format('d/m/Y') . ' - ' .
                                \Carbon\Carbon::parse($slot->jam)->format('H:i');
                        })->join('<br>');
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListLayanans::route('/'),
            'create' => Pages\CreateLayanan::route('/create'),
            'edit' => Pages\EditLayanan::route('/{record}/edit'),
        ];
    }
}
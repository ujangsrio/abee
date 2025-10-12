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

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Manajemen Layanan';
    protected static ?string $modelLabel = 'Layanan';
    protected static ?string $pluralModelLabel = 'Manajemen Layanan';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Layanan')
                ->schema([
                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Layanan')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('harga')
                        ->label('Harga (Rp)')
                        ->numeric()
                        ->required(),

                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->displayFormat('Y-m-d') // yyyy-mm-dd
                        ->required(),

                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi'),

                    Forms\Components\FileUpload::make('gambar')
                        ->label('Gambar')
                        ->image()
                        ->directory('layanan-images')
                        ->imagePreviewHeight('150px')
                        ->downloadable(),

                    Forms\Components\Select::make('promo_id')
                        ->label('Promo')
                        ->options(Promo::query()->pluck('nama_promo', 'id'))
                        ->searchable()
                        ->placeholder('Pilih Promo (Opsional)')
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            $set('is_promo', $state ? true : false);
                        }),

                    Forms\Components\Toggle::make('is_promo')
                        ->label('Gunakan Promo')
                        ->default(false)
                        ->reactive()
                        ->disabled(), 
                ])->columns(2),

            Forms\Components\Section::make('Slot Waktu')
                ->description('Tambahkan jam layanan yang tersedia')
                ->schema([
                    Forms\Components\Repeater::make('slots')
                        ->label('Daftar Slot')
                        ->relationship('slots')
                        ->schema([
                            Forms\Components\DatePicker::make('tanggal')
                                ->label('Tanggal')
                                ->displayFormat('Y-m-d')
                                ->required(),
                            Forms\Components\TimePicker::make('jam')
                                ->label('Jam')
                                ->withoutSeconds()
                                ->required(),
                        ])
                        ->columns(2)
                        ->createItemButtonLabel('Tambah Slot'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('harga')->label('Harga')->money('IDR', true),
                Tables\Columns\TextColumn::make('tanggal')->label('Tanggal')->date('Y-m-d'),
                Tables\Columns\TextColumn::make('slots_count')->counts('slots')->label('Jumlah Slot'),
                Tables\Columns\ImageColumn::make('gambar')->label('Gambar')->height(60),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
}

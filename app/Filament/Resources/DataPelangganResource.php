<?php

namespace App\Filament\Resources;

use App\Models\Customer;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\DataPelangganResource\Pages;

class DataPelangganResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Data Pelanggan';

    protected static ?string $modelLabel = 'Pelanggan';

    protected static ?string $pluralModelLabel = 'Data Pelanggan';
    protected static ?string $navigationGroup = 'Pelanggan';

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('user.email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('whatsapp')
                            ->label('No. WhatsApp')
                            ->required()
                            ->tel()
                            ->maxLength(20),

                        Forms\Components\FileUpload::make('photo')
                            ->label('Foto Profil')
                            ->image()
                            ->directory('customer-photos')
                            ->avatar()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Membership')
                    ->schema([
                        Forms\Components\Toggle::make('is_member')
                            ->label('Status Member')
                            ->onColor('success')
                            ->offColor('gray'),

                        Forms\Components\TextInput::make('kode_member')
                            ->label('Kode Member')
                            ->disabled()
                            ->maxLength(255)
                            ->visible(fn($get) => $get('is_member')),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('primary'),

                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('No. WhatsApp')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->iconColor('success'),

                Tables\Columns\IconColumn::make('is_member')
                    ->label('Member')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('kode_member')
                    ->label('Kode Member')
                    ->searchable()
                    ->placeholder('-')
                    ->color('primary')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->date('d-m-Y') // FORMAT DIPERBAIKI
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_member')
                    ->label('Hanya Member')
                    ->query(fn(Builder $query) => $query->where('is_member', true)),

                Tables\Filters\Filter::make('bukan_member')
                    ->label('Bukan Member')
                    ->query(fn(Builder $query) => $query->where('is_member', false)),

            ])
            ->emptyStateHeading('Belum ada data pelanggan')
            ->emptyStateDescription('Data pelanggan akan muncul di sini ketika ada yang mendaftar.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->deferLoading()
            ->striped();
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
            'index' => Pages\ListDataPelanggans::route('/'),
            'create' => Pages\CreateDataPelanggan::route('/create'),
            'edit' => Pages\EditDataPelanggan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['user'])->latest();
    }
}

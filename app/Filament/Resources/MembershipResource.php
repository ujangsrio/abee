<?php

namespace App\Filament\Resources;

use App\Models\CustomerMembership;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MembershipResource\Pages;

class MembershipResource extends Resource
{
    protected static ?string $model = CustomerMembership::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Data Membership';

    protected static ?string $modelLabel = 'Data Membership';


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
                Forms\Components\TextInput::make('name')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('whatsapp')
                    ->label('No. WhatsApp')
                    ->required()
                    ->maxLength(20),

                Forms\Components\TextInput::make('member_code')
                    ->label('Kode Membership')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                Forms\Components\DatePicker::make('expired_at')
                    ->label('Tanggal Expired')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('No.')
                    ->rowIndex(),
                    // ->sortable(false),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                    // ->sortable(),

                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('No. WhatsApp'),
                    // ->searchable(),

                Tables\Columns\TextColumn::make('member_code')
                    ->label('Kode Membership')
                    ->searchable()
                    // ->sortable()
                    ->color('primary')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->date('d M Y'),
                    // ->sortable(),

                // Tables\Columns\TextColumn::make('expired_at')
                //     ->label('Tanggal Expired')
                //     ->date('d M Y')
                //     ->sortable()
                //     ->color(fn($record) => $record->expired_at->isPast() ? 'danger' : 'success')
                //     ->description(fn($record) => $record->expired_at->isPast() ? 'Expired' : 'Aktif'),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label('Membership Aktif')
                    ->query(fn(Builder $query) => $query->where('expired_at', '>=', now())),

                Tables\Filters\Filter::make('expired')
                    ->label('Membership Expired')
                    ->query(fn(Builder $query) => $query->where('expired_at', '<', now())),
            ])
            // ->actions([
            //     Tables\Actions\EditAction::make(),
            //     Tables\Actions\DeleteAction::make(),
            // ])
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ])
            ->emptyStateHeading('Belum ada pelanggan membership')
            ->emptyStateDescription('Data pelanggan membership akan muncul di sini.')
            ->emptyStateIcon('heroicon-o-user-group');
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
            'index' => Pages\ListMemberships::route('/'),
            // 'create' => Pages\CreateMembership::route('/create'),
            // 'edit' => Pages\EditMembership::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->latest();
    }
}

<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Filament\Resources\PengaturanResource\Pages;

class PengaturanResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $modelLabel = 'Pengaturan';

    protected static ?string $pluralModelLabel = 'Pengaturan Akun Admin';

    protected static ?int $navigationSort = 999;

    // Hanya tampilkan data admin yang sedang login
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Profil Admin')
                    ->description('Kelola informasi profil dan akun admin Anda.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->minLength(5) // ⬅️ PERBAIKAN: Minimal 5 karakter
                            ->maxLength(50) // ⬅️ PERBAIKAN: Maksimal 50 karakter
                            ->placeholder('Masukkan nama lengkap admin'),

                        Forms\Components\TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder('email@example.com'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Keamanan Akun')
                    ->description('Perbarui kata sandi untuk meningkatkan keamanan akun.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Kata Sandi Saat Ini')
                            ->password()
                            ->requiredWith('new_password')
                            ->rule('current_password')
                            ->dehydrated(false)
                            ->placeholder('Masukkan kata sandi saat ini'),

                        Forms\Components\TextInput::make('new_password')
                            ->label('Kata Sandi Baru')
                            ->password()
                            ->rule(Password::default())
                            ->minLength(8)
                            ->same('new_password_confirmation')
                            ->dehydrated(fn($state) => filled($state))
                            ->dehydrateStateUsing(fn($state) => filled($state) ? Hash::make($state) : null)
                            ->placeholder('Masukkan kata sandi baru (min. 8 karakter)'),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Konfirmasi Kata Sandi Baru')
                            ->password()
                            ->requiredWith('new_password')
                            ->dehydrated(false)
                            ->placeholder('Konfirmasi kata sandi baru'),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Forms\Components\Placeholder::make('role')
                            ->label('Role')
                            ->content(fn($record) => $record?->role === 'admin' ? 'Administrator' : 'Customer'),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Tanggal Dibuat')
                            ->content(fn($record) => $record?->created_at?->format('d M Y, H:i')),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Terakhir Diupdate')
                            // ⬅️ PERBAIKAN: Menambahkan 'H:i' untuk menampilkan jam
                            ->content(fn($record) => $record?->updated_at?->format('d M Y, H:i')), 
                    ])
                    ->columns(2)
                    ->visible(fn($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'success',
                        'customer' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d-m-y')
                    ->sortable(),
                
                // ⬅️ PERBAIKAN: Menambahkan kolom updated_at dengan format tanggal dan waktu
                Tables\Columns\TextColumn::make('updated_at') 
                    ->label('Diupdate')
                    ->dateTime('d-m-y, H:i') 
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Profil'),
            ])
            ->bulkActions([
                //
            ])
            ->emptyStateHeading('Data admin tidak ditemukan')
            ->emptyStateDescription('')
            ->modifyQueryUsing(fn($query) => $query->where('id', Auth::id()));
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
            'index' => Pages\ListPengaturans::route('/'),
            'create' => Pages\CreatePengaturan::route('/create'),
            'edit' => Pages\EditPengaturan::route('/{record}/edit'),
        ];
    }

    // Nonaktifkan pembuatan data baru
    public static function canCreate(): bool
    {
        return false;
    }

    // Nonaktifkan penghapusan data
    public static function canDelete($record): bool
    {
        return false;
    }

    // Hanya admin yang bisa mengakses
    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->role === 'admin';
    }
}
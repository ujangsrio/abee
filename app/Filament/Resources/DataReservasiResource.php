<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataReservasiResource\Pages;
use App\Models\CustomerBooking;
use App\Models\Customer;
use App\Models\CustomerService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;

class DataReservasiResource extends Resource
{
    protected static ?string $model = CustomerBooking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Manajemen Reservasi';

    protected static ?string $modelLabel = 'Manajemen Reservasi';

    protected static ?string $pluralModelLabel = 'Manajemen Reservasi';

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
                        Forms\Components\Select::make('customer_id')
                            ->label('Pelanggan')
                            ->relationship('customer', 'name')
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $customer = Customer::find($state);
                                if ($customer) {
                                    $set('customer_name', $customer->name);
                                }
                            }),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Detail Reservasi')
                    ->schema([
                        Forms\Components\Select::make('service_id')
                            ->label('Layanan')
                            ->relationship('service', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->required()
                            ->native(false),
                        Forms\Components\TimePicker::make('time')
                            ->label('Jam')
                            ->required()
                            ->seconds(false),
                        Forms\Components\CheckboxList::make('tipe_layanan')
                            ->label('Tipe Layanan')
                            ->options([
                                'studio' => 'Studio',
                                'home_service' => 'Home Service',
                            ])
                            ->columns(2),
                    ])->columns(2),

                Forms\Components\Section::make('Pembayaran & Status')
                    ->schema([
                        Forms\Components\Select::make('tipe_pembayaran')
                            ->label('Tipe Pembayaran')
                            ->options([
                                'dp' => 'DP',
                                'full' => 'Full Payment',
                            ])
                            ->default('dp')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status Reservasi')
                            ->options([
                                'Menunggu' => 'Menunggu',
                                'Dikonfirmasi' => 'Dikonfirmasi',
                                'Dibatalkan' => 'Dibatalkan',
                                'Selesai' => 'Selesai',
                            ])
                            ->default('Menunggu')
                            ->required(),
                        Forms\Components\Select::make('status_dp')
                            ->label('Status DP')
                            ->options([
                                'Belum' => 'Belum',
                                'Lunas' => 'Lunas',
                            ])
                            ->default('Belum')
                            ->required(),
                        Forms\Components\FileUpload::make('bukti_transfer')
                            ->directory('bukti'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.whatsapp')
                    ->label('Kontak')
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('service.name')
                    ->label('Layanan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->formatStateUsing(function ($state) {

                        if (is_array($state)) {
                            return implode(', ', array_map(function ($item) {
                                return $item === 'home_service' ? 'Home Service' : 'Studio';
                            }, $state));
                        }

                        return $state ?? '-';
                    })
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('Jam')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('bukti_transfer')
                    ->label('Bukti Transfer')
                    ->disk('public') // penting! agar membaca dari storage/app/public
                    ->visibility('visible')
                    ->size(80) // opsional, bisa ubah sesuai kebutuhan
                    ->url(fn($record) => asset('storage/' . $record->bukti_transfer)) // agar bisa diklik
                    ->openUrlInNewTab(), // buka gambar di tab baru
                Tables\Columns\TextColumn::make('status_dp')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Reservasi')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Dikonfirmasi' => 'success',
                        'Dibatalkan' => 'danger',
                        'Selesai' => 'info',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Reservasi')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Dikonfirmasi' => 'Dikonfirmasi',
                        'Dibatalkan' => 'Dibatalkan',
                        'Selesai' => 'Selesai',
                    ]),
                SelectFilter::make('status_dp')
                    ->label('Status Pembayaran')
                    ->options([
                        'Belum' => 'Belum',
                        'Lunas' => 'Lunas',
                    ]),
                SelectFilter::make('service_id')
                    ->label('Layanan')
                    ->relationship('service', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('confirmDp')
                    ->label('Konfirmasi DP')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(CustomerBooking $record) => $record->status_dp === 'Belum' && $record->bukti_transfer)
                    ->requiresConfirmation()
                    ->action(function (CustomerBooking $record) {
                        $record->update([
                            'status_dp' => 'Lunas',
                            'status' => 'Dikonfirmasi',
                        ]);
                        Notification::make()
                            ->title('DP Dikonfirmasi')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('rejectDp')
                    ->label('Tolak DP')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(CustomerBooking $record) => $record->status_dp === 'Belum' && $record->bukti_transfer)
                    ->requiresConfirmation()
                    ->action(function (CustomerBooking $record) {
                        $record->update([
                            'status_dp' => 'Belum',
                            'status' => 'Dibatalkan',
                        ]);
                        Notification::make()
                            ->title('DP Ditolak')
                            ->warning()
                            ->send();
                    }),
                Tables\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'Menunggu' => 'Menunggu',
                                'Dikonfirmasi' => 'Dikonfirmasi',
                                'Dibatalkan' => 'Dibatalkan',
                                'Selesai' => 'Selesai',
                            ])
                            ->required(),
                    ])
                    ->action(function (CustomerBooking $record, array $data) {
                        $updates = ['status' => $data['status']];
                        if ($data['status'] === 'Selesai') {
                            $updates['status_dp'] = 'Lunas';
                        }
                        $record->update($updates);
                        Notification::make()
                            ->title('Status Diperbarui')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListDataReservasis::route('/'),
            'create' => Pages\CreateDataReservasi::route('/create'),
            'edit' => Pages\EditDataReservasi::route('/{record}/edit'),
        ];
    }
}

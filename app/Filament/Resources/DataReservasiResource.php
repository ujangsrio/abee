<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataReservasiResource\Pages;
use App\Models\CustomerBooking;
use App\Models\Customer;
use App\Models\Layanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

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
                            ->relationship('service', 'nama')
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
                            ->label('Bukti Transfer')
                            ->directory('bukti')
                            ->disk('public')
                            ->image()
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->downloadable()
                            ->openable()
                            ->previewable(true)
                            ->helperText('Format: JPG, JPEG, PNG. Maksimal 2MB.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.whatsapp')
                    ->label('WhatsApp')
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('service.nama')
                    ->label('Layanan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return collect($state)->map(function ($item) {
                                return match ($item) {
                                    'home_service' => 'Home Service',
                                    'studio' => 'Studio',
                                    default => ucfirst($item)
                                };
                            })->implode(', ');
                        }

                        // Jika state adalah string JSON, decode dulu
                        if (is_string($state)) {
                            try {
                                $decoded = json_decode($state, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                    return collect($decoded)->map(function ($item) {
                                        return match ($item) {
                                            'home_service' => 'Home Service',
                                            'studio' => 'Studio',
                                            default => ucfirst($item)
                                        };
                                    })->implode(', ');
                                }
                            } catch (\Exception $e) {
                                // Jika gagal decode, treat as string
                            }
                        }

                        // Fallback untuk string biasa
                        if (is_string($state)) {
                            return match ($state) {
                                'home_service' => 'Home Service',
                                'studio' => 'Studio',
                                default => ucfirst($state)
                            };
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
                    ->disk('public')
                    ->size(60)
                    ->square()
                    ->defaultImageUrl(url('/images/default-bukti.png')) // Fallback image
                    ->extraImgAttributes([
                        'class' => 'rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer',
                        'alt' => 'Bukti Transfer',
                        'onclick' => 'openImage(this.src)' // JavaScript untuk open image
                    ])
                    ->placeholder('Tidak ada bukti')
                    ->grow(false),
                Tables\Columns\TextColumn::make('tipe_pembayaran')
                    ->label('Tipe Bayar')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'full' => 'success',
                        'dp' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'full' => 'Lunas',
                        'dp' => 'DP',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status_dp')
                    ->label('Status DP')
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
                SelectFilter::make('tipe_pembayaran')
                    ->label('Tipe Pembayaran')
                    ->options([
                        'dp' => 'DP',
                        'full' => 'Lunas',
                    ]),
                SelectFilter::make('service_id')
                    ->label('Layanan')
                    ->relationship('service', 'nama'),
            ])
            ->actions([
                Tables\Actions\Action::make('viewBukti')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->visible(fn(CustomerBooking $record) => !empty($record->bukti_transfer))
                    ->action(function (CustomerBooking $record) {
                        if ($record->bukti_transfer && Storage::disk('public')->exists($record->bukti_transfer)) {
                            // Return URL untuk dibuka di tab baru
                            $url = Storage::disk('public');
                            return redirect($url);
                        }

                        Notification::make()
                            ->title('Bukti Transfer Tidak Ditemukan')
                            ->danger()
                            ->send();
                    })
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('confirmDp')
                    ->label('Konfirmasi DP')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn(CustomerBooking $record) =>
                        $record->status_dp === 'Belum' &&
                            !empty($record->bukti_transfer) &&
                            $record->status !== 'Dibatalkan'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi DP')
                    ->modalDescription('Apakah Anda yakin ingin mengkonfirmasi DP ini? Status akan berubah menjadi Lunas dan reservasi akan dikonfirmasi.')
                    ->action(function (CustomerBooking $record) {
                        $record->update([
                            'status_dp' => 'Lunas',
                            'status' => 'Dikonfirmasi',
                        ]);

                        Notification::make()
                            ->title('DP Berhasil Dikonfirmasi')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('rejectDp')
                    ->label('Tolak DP')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(
                        fn(CustomerBooking $record) =>
                        $record->status_dp === 'Belum' &&
                            !empty($record->bukti_transfer) &&
                            $record->status !== 'Dibatalkan'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Tolak DP')
                    ->modalDescription('Apakah Anda yakin ingin menolak DP ini? Reservasi akan dibatalkan.')
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
                            ->label('Status Reservasi')
                            ->options([
                                'Menunggu' => 'Menunggu',
                                'Dikonfirmasi' => 'Dikonfirmasi',
                                'Dibatalkan' => 'Dibatalkan',
                                'Selesai' => 'Selesai',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status_dp')
                            ->label('Status DP')
                            ->options([
                                'Belum' => 'Belum',
                                'Lunas' => 'Lunas',
                            ])
                            ->required(),
                    ])
                    ->action(function (CustomerBooking $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                            'status_dp' => $data['status_dp'],
                        ]);

                        Notification::make()
                            ->title('Status Berhasil Diperbarui')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Tidak ada reservasi')
            ->emptyStateDescription('Belum ada data reservasi yang tercatat.')
            ->emptyStateIcon('heroicon-o-calendar');
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

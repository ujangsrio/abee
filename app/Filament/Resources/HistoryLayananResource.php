<?php

namespace App\Filament\Resources;

use App\Models\CustomerBooking;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\HistoryLayananResource\Pages;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class HistoryLayananResource extends Resource
{
    protected static ?string $model = CustomerBooking::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'History Layanan';
    protected static ?string $modelLabel = 'History Layanan';
    protected static ?string $pluralModelLabel = 'History Layanan';
    protected static ?int $navigationSort = 3;

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
        return $form->schema([
            Forms\Components\Section::make('Informasi Booking')
                ->schema([
                    Forms\Components\TextInput::make('customer_name')
                        ->label('Nama Pelanggan')
                        ->disabled()
                        ->maxLength(255),

                    Forms\Components\Select::make('service_id')
                        ->label('Layanan')
                        ->relationship('service', 'nama')
                        ->disabled(),

                    Forms\Components\DatePicker::make('date')
                        ->label('Tanggal Booking')
                        ->disabled()
                        ->displayFormat('d-m-Y'), // FORMAT DIPERBAIKI

                    Forms\Components\TimePicker::make('time')
                        ->label('Waktu Booking')
                        ->disabled()
                        ->seconds(false),
                ])
                ->columns(2),

            Forms\Components\Section::make('Detail Layanan')
                ->schema([
                    Forms\Components\TextInput::make('tipe_layanan')
                        ->label('Tipe Layanan')
                        ->formatStateUsing(function ($state) {
                            if (is_array($state)) {
                                return collect($state)->map(fn($item) => match ($item) {
                                    'studio' => 'Studio',
                                    'home_service' => 'Home Service',
                                    default => ucfirst(str_replace('_', ' ', $item)),
                                })->implode(', ');
                            }

                            if (is_string($state) && str_contains($state, '[')) {
                                $decoded = json_decode($state, true);
                                if (is_array($decoded)) {
                                    return collect($decoded)->map(fn($item) => match ($item) {
                                        'studio' => 'Studio',
                                        'home_service' => 'Home Service',
                                        default => ucfirst(str_replace('_', ' ', $item)),
                                    })->implode(', ');
                                }
                            }

                            return match ($state) {
                                'studio' => 'Studio',
                                'home_service' => 'Home Service',
                                default => ucfirst(str_replace('_', ' ', $state ?? '-')),
                            };
                        })
                        ->disabled(),

                    Forms\Components\TextInput::make('status')
                        ->label('Status Booking')
                        ->disabled(),

                    Forms\Components\TextInput::make('status_dp')
                        ->label('Status DP')
                        ->disabled(),

                    Forms\Components\TextInput::make('tipe_pembayaran')
                        ->label('Tipe Pembayaran')
                        ->disabled(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Bukti Transfer')
                ->schema([
                    Forms\Components\FileUpload::make('bukti_transfer')
                        ->label('Bukti Transfer')
                        ->image()
                        ->visibility('public')
                        ->directory('bukti-transfer')
                        ->disabled()
                        ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('service.nama')
                    ->label('Layanan')
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ?? '-'),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d-m-Y'), // FORMAT DIPERBAIKI

                Tables\Columns\TextColumn::make('time')
                    ->label('Waktu'),

                Tables\Columns\TextColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return collect($state)->map(fn($item) => match ($item) {
                                'studio' => 'Studio',
                                'home_service' => 'Home Service',
                                default => ucfirst($item),
                            })->implode(', ');
                        }
                        return $state ?? '-';
                    })
                    ->color(fn($state): string => str_contains($state, 'home_service') ? 'success' : 'primary'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Menunggu' => 'warning',
                        'Dikonfirmasi' => 'info',
                        'Selesai' => 'success',
                        'Dibatalkan' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Menunggu' => 'heroicon-o-clock',
                        'Dikonfirmasi' => 'heroicon-o-check-circle',
                        'Selesai' => 'heroicon-o-check-badge',
                        'Dibatalkan' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),

                Tables\Columns\TextColumn::make('status_dp')
                    ->label('Status DP')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Belum' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tipe_pembayaran')
                    ->label('Tipe Bayar')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'lunas' => 'success',
                        'dp' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\ImageColumn::make('bukti_transfer')
                    ->label('Bukti TF')
                    ->square()
                    ->visibility('public')
                    ->defaultImageUrl(asset('images/default-bukti.jpg'))
                    ->extraImgAttributes(['class' => 'object-cover'])
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d-m-Y H:i') // FORMAT DIPERBAIKI
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d-m-Y H:i') // FORMAT DIPERBAIKI
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Booking')
                    ->options([
                        'Menunggu' => 'Menunggu',
                        'Dikonfirmasi' => 'Dikonfirmasi',
                        'Selesai' => 'Selesai',
                        'Dibatalkan' => 'Dibatalkan',
                    ])
                    ->placeholder('Semua Status'),

                Tables\Filters\SelectFilter::make('status_dp')
                    ->label('Status DP')
                    ->options([
                        'Belum' => 'Belum Bayar',
                        'Lunas' => 'Lunas',
                    ])
                    ->placeholder('Semua Status DP'),

                Tables\Filters\SelectFilter::make('tipe_pembayaran')
                    ->label('Tipe Pembayaran')
                    ->options([
                        'dp' => 'DP',
                        'lunas' => 'Lunas',
                    ])
                    ->placeholder('Semua Tipe Pembayaran'),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('date_until')->label('Sampai Tanggal'),
                    ])
                    ->query(
                        fn(Builder $query, array $data): Builder => $query
                            ->when($data['date_from'], fn($q, $date) => $q->whereDate('date', '>=', $date))
                            ->when($data['date_until'], fn($q, $date) => $q->whereDate('date', '<=', $date))
                    )
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators['date_from'] = 'Dari: ' . Carbon::parse($data['date_from'])->format('d-m-Y');
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators['date_until'] = 'Sampai: ' . Carbon::parse($data['date_until'])->format('d-m-Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\Filter::make('hari_ini')
                    ->label('Hari Ini')
                    ->query(fn(Builder $query): Builder => $query->whereDate('date', today())),

                Tables\Filters\Filter::make('minggu_ini')
                    ->label('Minggu Ini')
                    ->query(fn(Builder $query): Builder => $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (CustomerBooking $record) {
                        $record->update(['status' => 'Dikonfirmasi']);
                        Notification::make()
                            ->title('Booking berhasil dikonfirmasi.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(CustomerBooking $record) => $record->status === 'Menunggu'),

                Tables\Actions\Action::make('selesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (CustomerBooking $record) {
                        $record->update(['status' => 'Selesai']);
                        Notification::make()
                            ->title('Layanan ditandai selesai.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn(CustomerBooking $record) => $record->status === 'Dikonfirmasi'),

                Tables\Actions\Action::make('batal')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (CustomerBooking $record) {
                        $record->update(['status' => 'Dibatalkan']);
                        Notification::make()
                            ->title('Booking dibatalkan.')
                            ->danger()
                            ->send();
                    })
                    ->visible(fn(CustomerBooking $record) => in_array($record->status, ['Menunggu', 'Dikonfirmasi'])),
            ])
            ->emptyStateHeading('Belum ada history layanan')
            ->emptyStateDescription('Data history layanan akan muncul di sini ketika ada booking.')
            ->emptyStateIcon('heroicon-o-clock')
            ->defaultSort('date', 'desc')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHistoryLayanans::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['service', 'customer'])
            ->orderByDesc('date');
    }
}

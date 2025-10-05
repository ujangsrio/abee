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
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Booking')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('service_id')
                            ->label('Layanan')
                            ->relationship('service', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),

                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal Booking')
                            ->required()
                            ->displayFormat('d M Y'),

                        Forms\Components\TimePicker::make('time')
                            ->label('Waktu Booking')
                            ->required()
                            ->seconds(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Layanan')
                    ->schema([
                        Forms\Components\Select::make('tipe_layanan')
                            ->label('Tipe Layanan')
                            ->multiple()
                            ->options([
                                'studio' => 'Studio',
                                'home_service' => 'Home Service',
                            ])
                            ->required(),

                        Forms\Components\Select::make('status')
                            ->label('Status Booking')
                            ->options([
                                'Menunggu' => 'Menunggu',
                                'Dikonfirmasi' => 'Dikonfirmasi',
                                'Selesai' => 'Selesai',
                                'Dibatalkan' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('Menunggu'),

                        Forms\Components\Select::make('status_dp')
                            ->label('Status DP')
                            ->options([
                                'Belum' => 'Belum',
                                'Lunas' => 'Lunas',
                            ])
                            ->required()
                            ->default('Belum'),

                        Forms\Components\Select::make('tipe_pembayaran')
                            ->label('Tipe Pembayaran')
                            ->options([
                                'dp' => 'DP',
                                'lunas' => 'Lunas',
                            ])
                            ->required()
                            ->default('dp'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Bukti Transfer')
                    ->schema([
                        Forms\Components\FileUpload::make('bukti_transfer')
                            ->label('Bukti Transfer')
                            ->image()
                            ->directory('bukti-transfer')
                            ->maxSize(2048)
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
                    // ->sortable(false),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    // ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('service.name')
                    ->label('Layanan')
                    // ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state ?? '-'),

                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y'),
                    // ->sortable(),

                Tables\Columns\TextColumn::make('time')
                    ->label('Waktu'),
            // ->sortable(),

            Tables\Columns\TextColumn::make('tipe_layanan')
                ->label('Tipe Layanan')
                ->badge()
                ->separator(',')
                ->formatStateUsing(function ($state) {
                    if (is_array($state)) {
                        return collect($state)->map(function ($item) {
                            return match ($item) {
                                'studio' => 'Studio',
                                'home_service' => 'Home Service',
                                default => $item
                            };
                        })->implode(', ');
                    }
                    return $state;
                })
                ->color(fn($state): string => match (true) {
                    str_contains($state, 'home_service') => 'success',
                    default => 'primary'
                }),

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
                    ->defaultImageUrl(url('/images/default-bukti.jpg'))
                    ->extraImgAttributes(['class' => 'object-cover'])
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['date_from'] ?? null) {
                            $indicators['date_from'] = 'Dari: ' . \Carbon\Carbon::parse($data['date_from'])->format('d M Y');
                        }
                        if ($data['date_until'] ?? null) {
                            $indicators['date_until'] = 'Sampai: ' . \Carbon\Carbon::parse($data['date_until'])->format('d M Y');
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('primary'),

                    Tables\Actions\EditAction::make()
                        ->color('warning'),

                    Tables\Actions\Action::make('konfirmasi')
                        ->label('Konfirmasi')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (CustomerBooking $record) {
                            $record->update(['status' => 'Dikonfirmasi']);

                            // Notifikasi atau aksi lainnya
                        })
                        ->visible(fn(CustomerBooking $record) => $record->status === 'Menunggu'),

                    Tables\Actions\Action::make('selesai')
                        ->label('Selesai')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (CustomerBooking $record) {
                            $record->update(['status' => 'Selesai']);
                        })
                        ->visible(fn(CustomerBooking $record) => $record->status === 'Dikonfirmasi'),

                    Tables\Actions\Action::make('batal')
                        ->label('Batalkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (CustomerBooking $record) {
                            $record->update(['status' => 'Dibatalkan']);
                        })
                        ->visible(fn(CustomerBooking $record) => in_array($record->status, ['Menunggu', 'Dikonfirmasi'])),

                    Tables\Actions\DeleteAction::make()
                        ->color('danger'),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status Baru')
                                ->options([
                                    'Menunggu' => 'Menunggu',
                                    'Dikonfirmasi' => 'Dikonfirmasi',
                                    'Selesai' => 'Selesai',
                                    'Dibatalkan' => 'Dibatalkan',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, $records) {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading('Belum ada history layanan')
            ->emptyStateDescription('Data history layanan akan muncul di sini ketika ada booking.')
            ->emptyStateIcon('heroicon-o-clock')
            ->deferLoading()
            ->defaultSort('date', 'desc')
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
            'index' => Pages\ListHistoryLayanans::route('/'),
            'create' => Pages\CreateHistoryLayanan::route('/create'),
            'edit' => Pages\EditHistoryLayanan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['service', 'customer']);
    }
}

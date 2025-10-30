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
use Illuminate\Database\Eloquent\Model; // <<< DIPERLUKAN UNTUK HOOK handleRecordUpdate
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class LayananResource extends Resource
{
    protected static ?string $model = Layanan::class;
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Manajemen Layanan';
    protected static ?string $modelLabel = 'Layanan';
    protected static ?string $pluralModelLabel = 'Manajemen Layanan';

    protected static ?string $navigationGroup = 'Manajemen';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Layanan')
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
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->columnSpan(1),

                    Forms\Components\Select::make('promo_id')
                        ->label('Pilih Promo (Opsional)')
                        ->relationship(
                            'promo', 
                            'nama_promo', 
                            fn (Builder $query) => $query->whereDate('tanggal_berakhir', '>=', now())
                        )
                        ->placeholder('Tidak ada promo yang diterapkan')
                        ->nullable()
                        ->searchable()
                        ->preload()
                        ->helperText('Pilih promo aktif yang ingin diterapkan.')
                        ->columnSpan(1),

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
                ])->columns(2),

            Forms\Components\Section::make('Tipe Layanan')
                ->schema([
                    Forms\Components\CheckboxList::make('tipe_layanan')
                        ->label('Tipe Layanan yang Tersedia')
                        ->options([
                            'studio' => 'Studio',
                            'home_service' => 'Home Service',
                        ])
                        ->default(['studio'])
                        ->columns(1)
                        ->helperText('Pilih tipe layanan yang tersedia untuk layanan ini.')
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Jadwal Pelayanan Berulang (Mingguan)')
                ->description('Atur jam operasional standar yang berulang setiap minggu.')
                ->schema([
                    Forms\Components\TimePicker::make('default_start_time')
                        ->label('Jam Mulai Default')
                        ->required()
                        ->seconds(false)
                        ->default('07:00')
                        ->columnSpan(1),

                    Forms\Components\TimePicker::make('default_end_time')
                        ->label('Jam Selesai Default')
                        ->required()
                        ->seconds(false)
                        ->default('16:00')
                        ->columnSpan(1),

                    Forms\Components\CheckboxList::make('available_days')
                        ->label('Hari Aktif')
                        ->options([
                            '1' => 'Senin',
                            '2' => 'Selasa',
                            '3' => 'Rabu',
                            '4' => 'Kamis',
                            '5' => 'Jumat',
                            '6' => 'Sabtu',
                            '0' => 'Minggu',
                        ])
                        ->columns(3)
                        ->required()
                        ->default(['1', '2', '3', '4', '5'])
                        ->helperText('Pilih hari-hari di mana jam default berlaku.')
                        ->columnSpanFull(),
                    
                ])->columns(2),
            
            Forms\Components\Section::make('Jadwal Pengecualian (Tanggal Spesifik)')
                ->description('Atur jam yang berbeda atau hari libur untuk tanggal tertentu. Jadwal ini akan menimpa jadwal mingguan.')
                ->schema([
                    Forms\Components\Repeater::make('exception_schedule')
                        ->label('Daftar Pengecualian Tanggal')
                        ->schema([
                            Forms\Components\DatePicker::make('date')
                                ->label('Tanggal Pengecualian')
                                ->required()
                                ->minDate(now())
                                ->columnSpan(1),
                            
                            Forms\Components\TimePicker::make('start_time')
                                ->label('Jam Mulai Khusus (Kosongkan = Tutup)')
                                ->seconds(false)
                                ->nullable()
                                ->columnSpan(1),
                            
                            Forms\Components\TimePicker::make('end_time')
                                ->label('Jam Selesai Khusus (Kosongkan = Tutup)')
                                ->seconds(false)
                                ->nullable()
                                ->columnSpan(1),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->defaultItems(0)
                        ->helperText('Kosongkan Jam Mulai dan Jam Selesai pada baris pengecualian untuk menandai hari tersebut sebagai TUTUP.')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }

    // --- LOGIKA LIFECYCLE HOOKS (Memproses Jadwal) ---
    
    // Dipanggil saat data dimuat (Halaman Edit)
    public static function mutateFormDataBeforeFill(array $data): array
    {
        $recurring = $data['recurring_schedule'] ?? [];

        if (!empty($recurring) && is_array($recurring)) {
            $allSchedules = array_values($recurring);

            if (!empty($allSchedules) && is_array($allSchedules[0])) {
                $firstDaySchedule = $allSchedules[0];
                $data['default_start_time'] = $firstDaySchedule['start_time'] ?? null;
                $data['default_end_time'] = $firstDaySchedule['end_time'] ?? null;
            }
            
            $data['available_days'] = array_keys($recurring);
        }
        
        return $data;
    }
    
    // Fungsi pembantu untuk memproses data dari field input menjadi array PHP Layanan (yang akan di-cast Laravel menjadi JSON)
    protected static function processScheduleData(array $data): array
    {
        $recurringSchedule = [];
        $availableDays = $data['available_days'] ?? [];

        // 1. Menggabungkan Jam Default dan Hari Aktif menjadi recurring_schedule array
        if (empty($availableDays) || empty($data['default_start_time']) || empty($data['default_end_time'])) {
            // Jika hari aktif atau jam default kosong, set schedule menjadi array kosong
            $data['recurring_schedule'] = []; 
        } else {
            foreach ($availableDays as $dayOfWeek) {
                // Kita menyimpan string hari (0-6) sebagai key array untuk Filament
                $recurringSchedule[(string) $dayOfWeek] = [
                    'day_of_week' => (int) $dayOfWeek, 
                    'start_time' => $data['default_start_time'],
                    'end_time' => $data['default_end_time'],
                ];
            }
            $data['recurring_schedule'] = $recurringSchedule;
        }
        
        // 2. Membersihkan field temporer sebelum disimpan ke database
        unset($data['default_start_time'], $data['default_end_time'], $data['available_days']);
        
        // 3. Pastikan exception_schedule yang diulang juga diproses dan dibersihkan jika diperlukan
        // exception_schedule biasanya sudah berbentuk array dari form, tetapi ini memastikan konsistensi
        $data['exception_schedule'] = $data['exception_schedule'] ?? [];

        return $data;
    }

    // HOOK FALLBACK: Dipanggil sebelum data DISIMPAN (Halaman Edit)
    public static function mutateFormDataBeforeSave(array $data): array
    {
        return self::processScheduleData($data);
    }
    
    // *** HOOK UTAMA UNTUK UPDATE ***
    // Menimpa proses update default di page/action untuk memastikan data jadwal tersimpan
    public static function handleRecordUpdate(Model $record, array $data): Model
    {
        // Panggil fungsi pemrosesan untuk mendapatkan data jadwal yang benar
        $processedData = self::processScheduleData($data);
        
        // Mengisi model secara eksplisit (membutuhkan $fillable)
        $record->fill($processedData);
        $record->save();
        
        return $record;
    }

    // *** HOOK UTAMA UNTUK CREATE ***
    // Menimpa proses create default di page/action untuk memastikan data jadwal tersimpan
    public static function handleRecordCreate(array $data): Model
    {
        // Panggil fungsi pemrosesan
        $processedData = self::processScheduleData($data);
        
        // Buat dan simpan model
        $record = static::getModel()::create($processedData);
        
        return $record;
    }

    // --- AKHIR LOGIKA LIFECYCLE HOOKS ---

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

                Tables\Columns\TextColumn::make('Jadwal Aktif')
                    ->label('Jadwal Aktif')
                    ->getStateUsing(function (Layanan $record): string {
                        // Karena $casts sudah ada di model, $record->recurring_schedule harusnya array
                        $schedule = $record->recurring_schedule;
                        
                        // Perbaikan Defensif: Jika data bukan array (misal NULL, string) atau kosong
                        if (!is_array($schedule) || empty($schedule)) {
                            return 'Tidak Ada Jadwal';
                        }
                        
                        // Konversi keys hari (0-6) ke nama
                        $hariMap = [
                            '1' => 'Sen', '2' => 'Sel', '3' => 'Rab', 
                            '4' => 'Kam', '5' => 'Jum', '6' => 'Sab', '0' => 'Min',
                        ];
                        
                        // Ambil semua hari aktif
                        $days = array_keys($schedule);
                        $activeDays = array_map(fn($day) => $hariMap[$day] ?? '', $days);
                        
                        // Ambil jam dari jadwal hari pertama yang ditemukan
                        $firstSchedule = reset($schedule);
                        $start = $firstSchedule['start_time'] ?? '00:00';
                        $end = $firstSchedule['end_time'] ?? '00:00';
                        
                        // Tampilkan hasil
                        return implode(', ', $activeDays) . " (" . $start . ' - ' . $end . ')';
                    })
                    ->color(fn(Layanan $record) => is_array($record->recurring_schedule) && !empty($record->recurring_schedule) ? 'success' : 'warning')
                    ->icon(fn(Layanan $record) => is_array($record->recurring_schedule) && !empty($record->recurring_schedule) ? 'heroicon-o-calendar-days' : 'heroicon-o-x-circle')
                    ->badge(true)
                    ->tooltip('Jadwal berulang mingguan yang berlaku.'),
                
                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('promo.nama_promo')
                    ->label('Promo Diterapkan')
                    ->badge()
                    ->color('warning')
                    ->placeholder('Tidak Ada Promo')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TagsColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->getStateUsing(function ($record) {
                        if (!$record->tipe_layanan) return [];
                        return array_map(fn ($type) => match ($type) {
                            'studio' => 'Studio',
                            'home_service' => 'Home Service', 
                            default => ucfirst($type),
                        }, (array) $record->tipe_layanan);
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d-m-y')
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

                Tables\Filters\TernaryFilter::make('promo_id')
                    ->label('Status Promo')
                    ->nullable()
                    ->placeholder('Semua Layanan')
                    ->trueLabel('Ada Promo Diterapkan')
                    ->falseLabel('Tidak Ada Promo')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('promo_id'),
                        false: fn (Builder $query) => $query->whereNull('promo_id'),
                    ),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }
    
    // getPages tidak perlu diubah, karena Filament akan otomatis memanggil handleRecordCreate/Update 
    // jika didefinisikan di Resource, asalkan page yang digunakan adalah default.
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLayanans::route('/'),
            'create' => Pages\CreateLayanan::route('/create'),
            'edit' => Pages\EditLayanan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
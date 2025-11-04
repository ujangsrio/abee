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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            Forms\Components\Section::make('Informasi Layanan')
                ->schema([
                    Forms\Components\FileUpload::make('gambar')
                        ->label('Gambar Layanan')
                        ->disk('public')
                        ->directory('gambar_layanan')
                        ->image()
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                        ->helperText('Format: JPEG, PNG, JPG. Maksimal 2MB.')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('600')
                        ->imageResizeTargetHeight('400')
                        ->imagePreviewHeight('200')
                        ->loadingIndicatorPosition('left')
                        ->panelAspectRatio('2:1')
                        ->panelLayout('integrated')
                        ->removeUploadedFileButtonPosition('right')
                        ->uploadButtonPosition('left')
                        ->uploadProgressIndicatorPosition('left')
                        ->columnSpanFull()
                        ->visibility('public'),


                    Forms\Components\TextInput::make('nama')
                        ->label('Nama Layanan')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi')
                        ->maxLength(500)
                        ->rows(3)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('harga')
                        ->label('Harga (Rp)')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->columnSpan(1),

                    Forms\Components\Select::make('kategori')
                        ->label('Kategori Layanan')
                        ->options([
                            'kecantikan' => 'Kecantikan',
                            'kuku' => 'Perawatan Kuku',
                            'henna' => 'Henna',
                            'bulu_mata' => 'Bulu Mata',
                            'rambut' => 'Rambut',
                            'lainnya' => 'Lainnya',
                        ])
                        ->required()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('estimasi_durasi')
                        ->label('Estimasi Durasi (menit)')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(60)
                        ->suffix('menit')
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('kapasitas_per_slot')
                        ->label('Kapasitas per Slot')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(1)
                        ->columnSpan(1),

                    Forms\Components\Select::make('promo_id')
                        ->label('Promo')
                        ->relationship('promo', 'nama_promo')
                        ->placeholder('Tidak ada promo')
                        ->nullable()
                        ->columnSpan(2),
                ])->columns(2),

            Forms\Components\Section::make('Pengaturan Layanan')
                ->schema([
                    Forms\Components\CheckboxList::make('tipe_layanan')
                        ->label('Tipe Layanan')
                        ->options([
                            'studio' => 'Studio',
                            'home_service' => 'Home Service',
                        ])
                        ->default(['studio'])
                        ->columns(2)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true)
                        ->helperText('Nonaktifkan jika layanan tidak tersedia')
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('is_promo')
                        ->label('Sedang Promo')
                        ->default(false)
                        ->columnSpan(1),
                ])->columns(3),

            Forms\Components\Section::make('Jadwal Operasional')
                ->schema([
                    Forms\Components\DatePicker::make('periode_mulai')
                        ->label('Periode Mulai')
                        ->required()
                        ->columnSpan(1),

                    Forms\Components\DatePicker::make('periode_selesai')
                        ->label('Periode Selesai')
                        ->required()
                        ->columnSpan(1),

                    Forms\Components\TimePicker::make('jam_buka_default')
                        ->label('Jam Buka')
                        ->seconds(false)
                        ->required()
                        ->default('08:00')
                        ->columnSpan(1),

                    Forms\Components\TimePicker::make('jam_tutup_default')
                        ->label('Jam Tutup')
                        ->seconds(false)
                        ->required()
                        ->default('17:00')
                        ->columnSpan(1),

                    Forms\Components\CheckboxList::make('hari_operasional')
                        ->label('Hari Operasional')
                        ->options([
                            'senin' => 'Senin',
                            'selasa' => 'Selasa',
                            'rabu' => 'Rabu',
                            'kamis' => 'Kamis',
                            'jumat' => 'Jumat',
                            'sabtu' => 'Sabtu',
                            'minggu' => 'Minggu',
                        ])
                        ->default(['senin', 'selasa', 'rabu', 'kamis', 'jumat'])
                        ->columns(7)
                        ->required()
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->disk('public')
                    ->size(50)
                    ->defaultImageUrl(function (Layanan $record) {
                        return $record->getDefaultImageUrl();
                    })
                    ->extraImgAttributes([
                        'alt' => 'Gambar Layanan',
                        'class' => 'rounded-lg',
                    ]),


                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Layanan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('harga')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jadwal_operasional')
                    ->label('Jadwal Operasional')
                    ->getStateUsing(function ($record) {
                        $waktuOperasional = $record->waktu_operasional;

                        if (empty($waktuOperasional) || !is_array($waktuOperasional)) {
                            return 'Belum diatur';
                        }

                        $hariOperasional = $waktuOperasional['hari_operasional'] ?? [];
                        $jamBuka = $waktuOperasional['jam_buka_default'] ?? '08:00';
                        $jamTutup = $waktuOperasional['jam_tutup_default'] ?? '17:00';

                        if (empty($hariOperasional)) {
                            return 'Tidak ada jadwal';
                        }

                        $hariMap = [
                            'senin' => 'Sen',
                            'selasa' => 'Sel',
                            'rabu' => 'Rab',
                            'kamis' => 'Kam',
                            'jumat' => 'Jum',
                            'sabtu' => 'Sab',
                            'minggu' => 'Min',
                        ];

                        $hariSingkat = collect($hariOperasional)
                            ->map(function ($hari) use ($hariMap) {
                                return $hariMap[$hari] ?? ucfirst(substr($hari, 0, 3));
                            })
                            ->implode(', ');

                        return $hariSingkat . ' (' . $jamBuka . ' - ' . $jamTutup . ')';
                    })
                    ->description(function ($record) {
                        $waktuOperasional = $record->waktu_operasional;

                        if (empty($waktuOperasional) || !is_array($waktuOperasional)) {
                            return null;
                        }

                        $periodeMulai = $waktuOperasional['periode_mulai'] ?? null;
                        $periodeSelesai = $waktuOperasional['periode_selesai'] ?? null;

                        if ($periodeMulai && $periodeSelesai) {
                            return \Carbon\Carbon::parse($periodeMulai)->format('d/m/Y') . ' - ' .
                                \Carbon\Carbon::parse($periodeSelesai)->format('d/m/Y');
                        }

                        return 'Periode tidak ditentukan';
                    })
                    ->wrap()
                    ->limit(50)
                    ->tooltip(function ($record) {
                        $waktuOperasional = $record->waktu_operasional;

                        if (empty($waktuOperasional) || !is_array($waktuOperasional)) {
                            return 'Jadwal operasional belum diatur';
                        }

                        $hariOperasional = $waktuOperasional['hari_operasional'] ?? [];
                        $jamBuka = $waktuOperasional['jam_buka_default'] ?? '08:00';
                        $jamTutup = $waktuOperasional['jam_tutup_default'] ?? '17:00';
                        $periodeMulai = $waktuOperasional['periode_mulai'] ?? null;
                        $periodeSelesai = $waktuOperasional['periode_selesai'] ?? null;

                        $tooltip = "Jam Operasional: " . $jamBuka . " - " . $jamTutup . "\n";
                        $tooltip .= "Hari: " . implode(', ', $hariOperasional) . "\n";

                        if ($periodeMulai && $periodeSelesai) {
                            $tooltip .= "Periode: " . \Carbon\Carbon::parse($periodeMulai)->format('d/m/Y') .
                                " - " . \Carbon\Carbon::parse($periodeSelesai)->format('d/m/Y');
                        }

                        return $tooltip;
                    }),

                Tables\Columns\TextColumn::make('promo.nama_promo')
                    ->label('Nama Promo')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Tidak ada promo')
                    ->color('success')
                    ->weight('bold')
                    ->icon('heroicon-o-tag')
                    ->iconColor('success'),

                Tables\Columns\IconColumn::make('is_promo')
                    ->label('Promo Aktif')
                    ->boolean()
                    ->sortable(),


                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TagsColumn::make('tipe_layanan')
                    ->label('Tipe Layanan')
                    ->getStateUsing(function ($record) {
                        $tipeLayanan = $record->tipe_layanan;
                        if (is_string($tipeLayanan)) {
                            $tipeLayanan = json_decode($tipeLayanan, true);
                        }
                        return is_array($tipeLayanan) ? $tipeLayanan : [];
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'kecantikan' => 'Kecantikan',
                        'kuku' => 'Perawatan Kuku',
                        'henna' => 'Henna',
                        'bulu_mata' => 'Bulu Mata',
                        'rambut' => 'Rambut',
                        'lainnya' => 'Lainnya',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

                // Filter berdasarkan hari operasional
                Tables\Filters\SelectFilter::make('hari_operasional')
                    ->label('Hari Operasional')
                    ->options([
                        'senin' => 'Senin',
                        'selasa' => 'Selasa',
                        'rabu' => 'Rabu',
                        'kamis' => 'Kamis',
                        'jumat' => 'Jumat',
                        'sabtu' => 'Sabtu',
                        'minggu' => 'Minggu',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereJsonContains('waktu_operasional->hari_operasional', $data['value']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

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
}

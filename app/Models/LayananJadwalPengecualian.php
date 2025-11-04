<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananJadwalPengecualian extends Model
{
    use HasFactory;

    protected $table = 'layanan_jadwal_pengecualian';

    protected $fillable = [
        'layanan_id',
        'tanggal',
        'alasan',
        'jam_buka_khusus',
        'jam_tutup_khusus',
        'is_tutup',
    ];

    protected $casts = [
        'is_tutup' => 'boolean',
        'tanggal' => 'date',
        'jam_buka_khusus' => 'datetime',
        'jam_tutup_khusus' => 'datetime',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}

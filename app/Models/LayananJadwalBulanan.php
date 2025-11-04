<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananJadwalBulanan extends Model
{
    use HasFactory;

    protected $table = 'layanan_jadwal_bulanan';

    protected $fillable = [
        'layanan_id',
        'hari',
        'jam_buka',
        'jam_tutup',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'jam_buka' => 'datetime',
        'jam_tutup' => 'datetime',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}

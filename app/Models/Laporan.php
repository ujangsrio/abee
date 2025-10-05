<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode',
        'jenis_periode',
        'total_reservasi',
        'total_selesai',
        'total_dibatalkan',
        'total_pendapatan',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'total_pendapatan' => 'decimal:2',
    ];
}

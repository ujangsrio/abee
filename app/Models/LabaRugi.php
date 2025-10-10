<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabaRugi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori',
        'nama_item',
        'jumlah',
        'keterangan',
        'tanggal'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date'
    ];
}

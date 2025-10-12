<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga',
        'tanggal',
        'jam',
        'deskripsi',
        'gambar',
        'promo_id',
        'total_dipesan',
        'is_promo',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    public function slots()
    {
        return $this->hasMany(Slot::class, 'layanan_id');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }
}

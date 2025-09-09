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
        'deskripsi',
        'gambar',
        'promo_id',
        'jam',
    ];

    public function promo()
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }

    public function slots()
    {
        return $this->hasMany(\App\Models\Slot::class);
    }


}


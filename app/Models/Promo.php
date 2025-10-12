<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_promo',
        'deskripsi',
        'diskon',
        'hanya_member',
        'tanggal_berakhir',
    ];

    protected $casts = [
        'tanggal_berakhir' => 'date:Y-m-d',
    ];

    public function layanans()
    {
        return $this->hasMany(Layanan::class, 'promo_id');
    }
}

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
        'hanya_member' => 'boolean',
    ];

    public function layanan()
    {
        return $this->hasOne(Layanan::class, 'promo_id');
    }

}

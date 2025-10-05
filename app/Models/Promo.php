<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'tanggal_berakhir' => 'date',
    ];

    public function layanans(): HasMany
    {
        return $this->hasMany(Layanan::class);
    }

    // public function layanan()
    // {
    //     return $this->hasOne(Layanan::class, 'promo_id');
        
    // }


}

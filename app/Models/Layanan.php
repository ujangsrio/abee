<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'tipe_layanan',
    ];

    protected $casts = [
        // 'tanggal' => 'date',
        'is_promo' => 'boolean',
        'tipe_layanan' => 'array',
    ];

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    public function bookings()
    {
        return $this->hasMany(CustomerBooking::class, 'service_id');
    }
}

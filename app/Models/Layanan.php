<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'harga',
        'tanggal',
        'deskripsi',
        'gambar',
        'total_dipesan',
        'is_promo',
        'tipe_layanan',
        'durasi'
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'tanggal' => 'date',
        'is_promo' => 'boolean',
        'tipe_layanan' => 'array',
        'total_dipesan' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class, 'layanan_id');
    }

    public function bookings()
    {
        return $this->hasMany(CustomerBooking::class, 'service_id');
    }

    public function customerBookings()
    {
        return $this->hasMany(CustomerBooking::class, 'service_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_promo',
        'deskripsi',
        'diskon',
        'hanya_member',
        'tanggal_berakhir',
        'status',
    ];

    protected $casts = [
        'tanggal_berakhir' => 'date:Y-m-d',
    ];

    public function layanans()
    {
        return $this->hasMany(Layanan::class, 'promo_id');
    }
    public function updateStatusIfExpired()
    {
        if ($this->tanggal_berakhir->isPast() && $this->status === 'aktif') {
            $this->update(['status' => 'nonaktif']);
        }
    }
}

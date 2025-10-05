<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    //
    protected $fillable = [
        'pelanggan_id',
        'layanan_id',
        'tanggal',
        'jam',
        'harga',
        'bukti_transfer',
        'kontak',
        'tipe_layanan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

}
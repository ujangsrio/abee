<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'layanan_id',
        'tanggal',
        'jam',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
        // 'jam' bisa biarkan default (string)
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }
}

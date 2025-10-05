<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'layanan_id',
        'tanggal',
        'jam',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }
}


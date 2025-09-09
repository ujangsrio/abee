<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = ['layanan_id', 'tanggal', 'jam'];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class);
    }
}


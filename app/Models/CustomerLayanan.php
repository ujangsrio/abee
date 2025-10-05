<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLayanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'harga',
        'gambar',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];
}

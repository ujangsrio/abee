<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerLayanan extends Model
{
    use HasFactory;

    protected $table = 'customer_layanans'; 
    protected $fillable = ['nama', 'gambar', 'harga', 'deskripsi']; 
}

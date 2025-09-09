<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'deskripsi', 'harga']; 

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function pelanggans()
{
    return $this->hasMany(Pelanggan::class);
}

}
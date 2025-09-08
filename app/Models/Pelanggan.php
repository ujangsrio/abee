<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'email', 'whatsapp', 'alamat', 'membership_id'];

    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerMembership extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'whatsapp',
        'member_code',
        'expired_at',
    ];

    protected $casts = [
    'expired_at' => 'datetime',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
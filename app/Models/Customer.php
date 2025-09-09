<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'is_member',
        'kode_member',
        'whatsapp',
        'photo',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->belongsToMany(CustomerService::class, 'customer_service_customer', 'customer_id', 'service_id');
    }

    public function membership()
    {
        return $this->hasOne(CustomerMembership::class);
    }
}


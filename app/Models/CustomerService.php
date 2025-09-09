<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerService extends Model
{
    protected $fillable = ['name', 'image'];

    public function bookings()
    {
        return $this->hasMany(CustomerBooking::class, 'service_id');
    }
}

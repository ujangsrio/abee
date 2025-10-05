<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image',
    ];

    public function bookings()
    {
        return $this->hasMany(CustomerBooking::class, 'service_id');
    }
}

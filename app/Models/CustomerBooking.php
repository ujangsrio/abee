<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerBooking extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_name',
        'service_id',
        'date',
        'time',
        'tipe_layanan',
        'status',
        'status_dp',
        'tipe_pembayaran',
        'bukti_transfer',
        'variasi'
    ];

    protected $casts = [
        'tipe_layanan' => 'array',
        'variasi' => 'array',
        'date' => 'date'
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'service_id');
    }

    // Relasi ke model Layanan
    public function service()
    {
        return $this->belongsTo(Layanan::class, 'service_id');
    }

    // Relasi ke model Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // app/Models/Layanan.php  
    public function bookings()
    {
        return $this->hasMany(CustomerBooking::class, 'service_id');
    }
}

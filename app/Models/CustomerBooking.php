<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'service_id',
        'nama',
        'date',
        'time',
        'tipe_layanan',
        'status',
        'status_dp',
        'tipe_pembayaran',
        'variasi',
        'bukti_transfer',
    ];

    protected $casts = [
        // 'date' => 'date',
        'tipe_layanan' => 'array',
        'variasi' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(CustomerService::class, 'service_id');
    }

    public function layanan()
    {
        return $this->belongsTo(CustomerLayanan::class, 'service_id');
    }
}

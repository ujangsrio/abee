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
        'date',
        'time',
        'variasi',
        'bukti_transfer',
        'status',
    ];

    protected $casts = [
        'variasi' => 'array',
    ];


    public function service()
    {
        return $this->belongsTo(Layanan::class, 'service_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // public function layanan()
    // {
    //     return $this->belongsTo(Layanan::class)->withDefault([
    //         'harga' => 0,
    //         'nama' => 'Layanan Tidak Ditemukan'
    //     ]);
    // }
}

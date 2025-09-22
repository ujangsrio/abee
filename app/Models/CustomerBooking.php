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
        'status_dp',
        'tipe_pembayaran',
        'tipe_layanan',
    ];

    protected $casts = [
        'variasi' => 'array',
        'tipe_layanan' => 'array',
    ];

    public function service()
    {
        return $this->belongsTo(Layanan::class, 'service_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    protected static function booted()
    {
        static::creating(function ($booking) {
            // Default status awal
            if (empty($booking->status)) {
                $booking->status = 'Menunggu';
            }

            // Kalau langsung bayar full (status_dp = Lunas)
            if ($booking->status_dp === 'Lunas') {
                $booking->status = 'Dikonfirmasi';
            }
        });

        static::updating(function ($booking) {
            // Kalau status_dp jadi Lunas → otomatis status reservasi dikonfirmasi
            if ($booking->status_dp === 'Lunas') {
                $booking->status = 'Dikonfirmasi';
            }
        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'harga',
        'tanggal',
        'jam',
        'deskripsi',
        'gambar',
        'total_dipesan',
        'is_promo',
        'tipe_layanan',
    ];

    protected $casts = [
        'tipe_layanan' => 'array',
        'is_promo' => 'boolean',
        'harga' => 'integer',
        'total_dipesan' => 'integer',
        'tanggal' => 'date',
    ];

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function customerBookings()
    {
        return $this->hasMany(CustomerBooking::class, 'service_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Auto create slot ketika layanan dibuat/diupdate
        static::saved(function ($layanan) {
            $layanan->autoCreateOrUpdateSlot();
        });
    }

    public function autoCreateOrUpdateSlot()
    {
        // Jika ada tanggal dan jam di layanan, buat/update satu slot utama
        if ($this->tanggal && $this->jam) {
            $jam = $this->jam instanceof \Carbon\Carbon ? $this->jam->format('H:i:s') : $this->jam;

            // Cari slot utama (yang pertama) atau buat baru
            $mainSlot = $this->slots()->first();

            if (!$mainSlot) {
                // Buat slot baru
                $this->slots()->create([
                    'tanggal' => $this->tanggal,
                    'jam' => $jam,
                ]);
            } else {
                // Update slot yang sudah ada
                $mainSlot->update([
                    'tanggal' => $this->tanggal,
                    'jam' => $jam,
                ]);

                // Hapus slot tambahan jika ada (kita hanya butuh satu slot utama)
                $this->slots()->where('id', '!=', $mainSlot->id)->delete();
            }
        }
    }

    // Helper untuk mendapatkan jam utama (dari slot pertama)
    public function getJamUtamaAttribute()
    {
        $slot = $this->slots()->first();
        return $slot ? $slot->jam : $this->jam;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'harga',
        'deskripsi',
        'gambar',
        'total_dipesan',
        'is_promo',
        'tipe_layanan', 
        'promo_id', 
        'recurring_schedule', // Wajib untuk Jadwal Berulang
        'exception_schedule', // Wajib untuk Jadwal Pengecualian
    ];

    /**
     * Casting wajib agar kolom JSON (Repeater/CheckboxList) dan tipe data lainnya
     * dapat disimpan dan dibaca kembali dengan benar.
     */
    protected $casts = [
        // Casting untuk data array/JSON dari Filament:
        'tipe_layanan' => 'array',
        'recurring_schedule' => 'array',
        'exception_schedule' => 'array',
        
        // Casting dari model sebelumnya yang masih relevan:
        'is_promo' => 'boolean',
        'harga' => 'integer',
        'total_dipesan' => 'integer',
    ];

    public function promo(): BelongsTo
    {
        return $this->belongsTo(Promo::class, 'promo_id');
    }

    public function slots()
    {
        // Relasi ke slot dipertahankan
        return $this->hasMany(Slot::class);
    }

    public function customerBookings()
    {
        return $this->hasMany(CustomerBooking::class, 'service_id');
    }

    // CATATAN PENTING: Fungsi boot() dan autoCreateOrUpdateSlot()
    // TELAH DIHAPUS TOTAL untuk menyelesaikan error BadMethodCallException.
}
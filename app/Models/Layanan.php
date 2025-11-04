<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Layanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama',
        'harga',
        'deskripsi',
        'gambar',
        'durasi',
        'total_dipesan',
        'is_promo',
        'is_active',
        'kategori',
        'estimasi_durasi',
        'kapasitas_per_slot',
        'tipe_layanan',
        'waktu_operasional',
        'jadwal_khusus',
        'promo_id',
        'persyaratan',
        'catatan',
    ];

    protected $casts = [
        'tipe_layanan' => 'array',
        'waktu_operasional' => 'array',
        'jadwal_khusus' => 'array',
        'is_active' => 'boolean',
        'is_promo' => 'boolean',
        'harga' => 'decimal:2',
    ];

    // Accessor untuk gambar URL
    public function getGambarUrlAttribute()
    {
        if (!$this->gambar) {
            return $this->getDefaultImageUrl();
        }

        // Cek jika path sudah mengandung 'storage/', jika ya langsung return
        if (strpos($this->gambar, 'storage/') !== false) {
            return asset($this->gambar);
        }

        // Jika path relatif, tambahkan 'storage/'
        return asset('storage/' . $this->gambar);
    }

    // Method untuk default image
    public function getDefaultImageUrl()
    {
        $initial = strtoupper(substr($this->nama, 0, 1));
        return "https://ui-avatars.com/api/?name={$initial}&color=FFFFFF&background=8B5CF6&size=300&bold=true";
    }

    // Method untuk cek apakah gambar ada
    public function hasGambar()
    {
        return $this->gambar && Storage::disk('public')->exists($this->gambar);
    }

    // Method untuk handle waktu operasional
    public function setWaktuOperasionalAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['waktu_operasional'] = json_encode($value);
        } else {
            $this->attributes['waktu_operasional'] = $value;
        }
    }

    public function getWaktuOperasionalAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }

        return $value ?? [];
    }

    // Relation methods
    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function jadwalBulanan()
    {
        return $this->hasMany(LayananJadwalBulanan::class);
    }

    public function jadwalPengecualian()
    {
        return $this->hasMany(LayananJadwalPengecualian::class);
    }

    public function bookings()
    {
        return $this->hasMany(CustomerBooking::class, 'service_id');
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    // Boot method untuk handle delete
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($layanan) {
            // Hapus gambar saat delete - PERBAIKAN: pastikan path sesuai
            if ($layanan->gambar && Storage::disk('public')->exists($layanan->gambar)) {
                Storage::disk('public')->delete($layanan->gambar);
            }
        });
    }
}

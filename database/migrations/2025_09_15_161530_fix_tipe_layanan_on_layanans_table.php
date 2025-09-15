<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1️⃣ Ubah tipe kolom menjadi string agar bisa simpan 'studio' atau 'home_service'
        Schema::table('layanans', function (Blueprint $table) {
            $table->string('tipe_layanan')->change();
        });

        // 2️⃣ Update semua data JSON lama menjadi string
        $layanans = DB::table('layanans')->get();

        foreach ($layanans as $layanan) {
            $tipe = $layanan->tipe_layanan;

            // Jika JSON array, ambil hanya 'studio' jika ada
            if (@json_decode($tipe, true)) {
                $json = json_decode($tipe, true);

                if (in_array('studio', $json)) {
                    $newTipe = 'studio';
                } elseif (in_array('home_service', $json)) {
                    $newTipe = 'home_service';
                } else {
                    $newTipe = null;
                }

                DB::table('layanans')
                    ->where('id', $layanan->id)
                    ->update(['tipe_layanan' => $newTipe]);
            }
        }
    }

    public function down(): void
    {
        // rollback: ubah kembali ke longtext
        Schema::table('layanans', function (Blueprint $table) {
            $table->longText('tipe_layanan')->change();
        });
    }
};

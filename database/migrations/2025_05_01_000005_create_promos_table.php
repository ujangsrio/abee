<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('nama_promo')->nullable(false);
            $table->text('deskripsi')->nullable(false);
            $table->integer('diskon')->default(0); // ← dari migration tambahan
            $table->boolean('hanya_member')->default(false); // ← dari migration tambahan
            $table->date('tanggal_berakhir');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};

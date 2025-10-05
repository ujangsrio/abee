<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->enum('jenis_periode', ['harian', 'mingguan', 'bulanan', 'tahunan']);
            $table->integer('total_reservasi')->default(0);
            $table->integer('total_selesai')->default(0);
            $table->integer('total_dibatalkan')->default(0);
            $table->decimal('total_pendapatan', 12, 2)->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};

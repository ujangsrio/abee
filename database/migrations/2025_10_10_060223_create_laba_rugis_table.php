<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laba_rugis', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Pendapatan atau Pengeluaran
            $table->string('nama_item');
            $table->decimal('jumlah', 12, 2);
            $table->text('keterangan')->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laba_rugis');
    }
};

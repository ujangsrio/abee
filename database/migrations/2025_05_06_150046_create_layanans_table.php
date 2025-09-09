<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('layanans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->integer('harga');
            // $table->date('tanggal'); 
            $table->date('tanggal')->default(DB::raw('CURRENT_DATE'));
            // $table->time('jam')->nullable();
            $table->time('jam')->default(DB::raw('CURRENT_TIME'));

            $table->string('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->unsignedBigInteger('promo_id')->nullable();
            $table->foreign('promo_id')->references('id')->on('promos')->onDelete('set null');
            $table->integer('total_dipesan')->default(0);
            $table->boolean('is_promo')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanans');
    }
};

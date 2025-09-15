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
        Schema::table('customer_bookings', function (Blueprint $table) {
            // Drop foreign key yang salah
            $table->dropForeign(['service_id']);

            // Tambahkan foreign key yang benar
            $table->foreign('service_id')->references('id')->on('layanans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_bookings', function (Blueprint $table) {
            // Kembalikan ke foreign key lama (jika diperlukan rollback)
            $table->dropForeign(['service_id']);
            $table->foreign('service_id')->references('id')->on('customer_layanans')->onDelete('cascade');
        });
    }
};

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
            $table->string('dp_status')->default('Belum')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_bookings', function (Blueprint $table) {
            $table->dropColumn('dp_status');
        });
    }
};

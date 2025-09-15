<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('customer_bookings', function (Blueprint $table) {
            $table->string('tipe_layanan')->nullable()->after('time');
        });
    }

    public function down()
    {
        Schema::table('customer_bookings', function (Blueprint $table) {
            $table->dropColumn('tipe_layanan');
        });
    }
};

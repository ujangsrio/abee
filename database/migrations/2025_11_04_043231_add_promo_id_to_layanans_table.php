<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('layanans', function (Blueprint $table) {
            $table->foreignId('promo_id')
                ->nullable()
                ->constrained('promos')
                ->onDelete('set null')
                ->after('tipe_layanan');
        });
    }

    public function down()
    {
        Schema::table('layanans', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
            $table->dropColumn('promo_id');
        });
    }
};

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
        // Pastikan tabel layanans ada sebelum mencoba menambah kolom
        if (Schema::hasTable('layanans')) {
            Schema::table('layanans', function (Blueprint $table) {
                // Kolom recurring_schedule: untuk jadwal mingguan berulang (JSON)
                if (!Schema::hasColumn('layanans', 'recurring_schedule')) {
                    $table->json('recurring_schedule')->nullable()->after('promo_id');
                }
                
                // Kolom exception_schedule: untuk jadwal pengecualian tanggal spesifik (JSON)
                if (!Schema::hasColumn('layanans', 'exception_schedule')) {
                    $table->json('exception_schedule')->nullable()->after('recurring_schedule');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Logika rollback (menghapus kolom)
        Schema::table('layanans', function (Blueprint $table) {
            if (Schema::hasColumn('layanans', 'exception_schedule')) {
                $table->dropColumn('exception_schedule');
            }
            if (Schema::hasColumn('layanans', 'recurring_schedule')) {
                $table->dropColumn('recurring_schedule');
            }
        });
    }
};
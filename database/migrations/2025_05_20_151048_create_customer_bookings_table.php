<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade');
            $table->string('customer_name');
            $table->foreignId('service_id')->constrained('customer_layanans')->onDelete('cascade');
            $table->date('date');
            $table->string('time');
            $table->string('status')->default('Menunggu');
            $table->json('variasi')->nullable();
            $table->string('bukti_transfer')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_bookings');
    }
};

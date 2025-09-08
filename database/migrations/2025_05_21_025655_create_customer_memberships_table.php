<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerMembershipsTable extends Migration
{
    public function up()
    {
        Schema::create('customer_memberships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('name');
            $table->string('whatsapp');
            $table->string('member_code')->unique();
            $table->date('expired_at');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_memberships');
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaundryServicesTable extends Migration
{
    public function up()
    {
        Schema::create('laundry_services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('price_per_kg', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laundry_services');
    }
}
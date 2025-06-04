<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('laundry_service_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_kg', 8, 2);
            $table->decimal('unit_price', 8, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'laundry_service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
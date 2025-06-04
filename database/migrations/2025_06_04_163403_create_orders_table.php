<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->enum('status', ['pending', 'in_progress', 'ready', 'completed', 'cancelled'])
                  ->default('pending');
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->datetime('pickup_date')->nullable();
            $table->datetime('delivery_date')->nullable();
            $table->text('special_instructions')->nullable();
            $table->boolean('is_express')->default(false);
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index('order_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
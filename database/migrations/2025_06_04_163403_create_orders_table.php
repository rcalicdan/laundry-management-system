<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\OrderStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', OrderStatus::values())
                ->default(OrderStatus::PENDING->value);
            $table->decimal('total_amount', 10, 2)->default(0);
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

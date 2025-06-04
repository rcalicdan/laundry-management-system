<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', PaymentMethod::values());
            $table->enum('status', PaymentStatus::values())
                ->default(PaymentStatus::PENDING->value);
            $table->string('transaction_id')->nullable()->unique();
            $table->datetime('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

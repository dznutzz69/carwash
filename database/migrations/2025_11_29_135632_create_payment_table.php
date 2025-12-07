<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')
                ->constrained('appointments')
                ->onDelete('cascade');

            // Price stored as proper decimal for currency
            $table->decimal('amount', 10, 2)->default(0);

            // pending = waiting, paid = completed, cancelled = void
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');

            // optional payment method (cash, gcash, etc.)
            $table->string('method')->nullable();

            // marks when payment was completed
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

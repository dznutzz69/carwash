<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // For Flutter registered customers (users)
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('cascade');

            // For walk-in customers created in admin
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('customers')
                  ->onDelete('cascade');

            $table->foreignId('service_id')
                  ->constrained('services')
                  ->onDelete('cascade');

            $table->date('date');
            $table->string('time');
            $table->string('status')->default('pending'); // pending / approved / completed / cancelled

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

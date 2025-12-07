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
    $table->foreignId('appointment_id')->constrained('appointments')->onDelete('cascade');
    $table->integer('amount');
    $table->string('status')->default('paid'); // or unpaid
    $table->string('method')->nullable();
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

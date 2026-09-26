<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('cash_in_hand', 12, 2)->default(0.00); // Opening float
            $table->decimal('cash_sales', 12, 2)->default(0.00); // System total cash sales
            $table->decimal('total_cash_submitted', 12, 2)->nullable(); // Cashier counted amount
            $table->decimal('difference', 12, 2)->default(0.00); // Shortage (-) or Surplus (+)
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};

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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('cash_register_id')->nullable()->constrained('cash_registers')->nullOnDelete();
            $table->timestamp('order_date')->useCurrent();
            $table->integer('total_items')->default(0);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->enum('discount_type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('tax_percentage', 5, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('total_cost', 12, 2)->default(0.00); // Cost of goods sold (COGS)
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('due_amount', 12, 2)->default(0.00);
            $table->decimal('change_amount', 12, 2)->default(0.00);
            $table->enum('payment_type', ['cash', 'card', 'mobile_banking', 'credit', 'split'])->default('cash');
            $table->enum('payment_status', ['paid', 'partial', 'due'])->default('paid');
            $table->enum('order_status', ['completed', 'hold', 'cancelled'])->default('completed');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

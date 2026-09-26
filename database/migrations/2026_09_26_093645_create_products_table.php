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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->string('name');
            $table->string('code')->unique(); // Barcode / SKU
            $table->string('barcode_type')->default('C128');
            $table->decimal('cost_price', 12, 2)->default(0.00); // Buying price / COGS
            $table->decimal('selling_price', 12, 2); // Retail selling price
            $table->decimal('stock_quantity', 12, 2)->default(0.00); // Inventory count
            $table->decimal('alert_quantity', 12, 2)->default(5.00); // Low stock alert threshold
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

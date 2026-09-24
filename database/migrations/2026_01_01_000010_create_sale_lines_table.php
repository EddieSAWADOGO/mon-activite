<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('stock_unit_id')->constrained('stock_units');
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('default_unit_price', 15, 2);
            $table->string('discount_reason')->nullable();
            $table->decimal('subtotal', 15, 2);
            $table->foreignId('source_stock_unit_id')->nullable()->constrained('stock_units'); // Pour cassure
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_lines');
    }
};

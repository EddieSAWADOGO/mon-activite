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
        Schema::create('stock_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_base_unit')->default(false);
            $table->decimal('base_unit_equivalent', 12, 4)->default(1.0000);
            $table->unsignedBigInteger('default_selling_price')->default(0);
            $table->decimal('low_stock_threshold', 12, 4)->default(0.0000);
            $table->decimal('current_stock', 12, 4)->default(0.0000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_units');
    }
};

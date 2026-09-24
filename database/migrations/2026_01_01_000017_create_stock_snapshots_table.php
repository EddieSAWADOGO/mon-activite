<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('stock_unit_id')->constrained('stock_units')->cascadeOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->dateTime('snapshot_date');
            $table->timestamps();

            $table->unique(['year', 'month', 'stock_unit_id'], 'unique_monthly_stock_unit_snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_snapshots');
    }
};

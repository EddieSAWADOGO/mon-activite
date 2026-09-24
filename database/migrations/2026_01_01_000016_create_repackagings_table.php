<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repackagings', function (Blueprint $table) {
            $table->id();
            $table->string('repackaging_number')->unique();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('source_stock_unit_id')->constrained('stock_units')->cascadeOnDelete();
            $table->decimal('source_quantity', 15, 2);
            $table->foreignId('target_stock_unit_id')->constrained('stock_units')->cascadeOnDelete();
            $table->decimal('target_quantity', 15, 2);
            $table->dateTime('repackaging_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repackagings');
    }
};

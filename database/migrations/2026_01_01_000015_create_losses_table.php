<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('losses', function (Blueprint $table) {
            $table->id();
            $table->string('loss_number')->unique();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('stock_unit_id')->constrained('stock_units')->cascadeOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->string('reason');
            $table->dateTime('loss_date');
            $table->text('notes')->nullable();
            $table->foreignId('customer_return_id')->nullable()->constrained('customer_returns')->nullOnDelete();
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('losses');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('stock_unit_id')->constrained('stock_units')->cascadeOnDelete();
            $table->decimal('quantity', 15, 2);
            $table->string('reason');
            $table->string('status')->default('pending'); // pending, restocked, discarded
            $table->dateTime('return_date');
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->foreignId('validated_by_user_id')->nullable()->constrained('users');
            $table->dateTime('validated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_returns');
    }
};

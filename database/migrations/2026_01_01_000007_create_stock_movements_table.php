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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('stock_unit_id')->constrained('stock_units');
            $table->string('type'); // MovementType enum value
            $table->decimal('quantity', 12, 4);
            $table->string('direction'); // 'in' or 'out'
            $table->nullableMorphs('reference'); // reference_type, reference_id
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->dateTime('movement_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

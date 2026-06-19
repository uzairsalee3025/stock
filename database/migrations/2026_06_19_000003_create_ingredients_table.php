<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            // Measurement unit: kg, gram, liter, ml, pcs, etc.
            $table->string('unit')->default('kg');
            // Total available quantity = sum of all supplier stock entries - usages.
            $table->decimal('available_quantity', 14, 3)->default(0);
            // Optional: drives low-stock alerts/reports (not a primary create-form field).
            $table->decimal('low_stock_threshold', 14, 3)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Supplier-wise stock entries. Each row is one supplier's quantity for an
     * ingredient; their quantities sum into ingredients.available_quantity.
     */
    public function up(): void
    {
        Schema::create('ingredient_stock_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->string('supplier_name');
            $table->decimal('quantity', 14, 3);
            $table->decimal('price', 14, 2)->default(0);
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('supplier_name');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_stock_entries');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ingredient usage / "remove stock" entries (stock OUT).
     */
    public function up(): void
    {
        Schema::create('ingredient_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 14, 3);
            $table->date('usage_date');
            // Notes / purpose of the usage (optional).
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('usage_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_usages');
    }
};

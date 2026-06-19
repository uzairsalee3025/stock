<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sale / deduction history. Each row reduces the product's quantity_stock.
     */
    public function up(): void
    {
        Schema::create('product_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('sale_quantity');
            $table->date('sale_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sale_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sales');
    }
};

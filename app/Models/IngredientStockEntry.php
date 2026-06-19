<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredientStockEntry extends Model
{
    protected $fillable = [
        'ingredient_id',
        'supplier_name',
        'quantity',
        'price',
        'date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'price' => 'decimal:2',
            'date' => 'date',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    /** Total value of this supplier entry (quantity * price). */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->quantity * (float) $this->price;
    }
}

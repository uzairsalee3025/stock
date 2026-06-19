<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = [
        'ingredient_category_id',
        'name',
        'unit',
        'available_quantity',
        'low_stock_threshold',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'available_quantity' => 'decimal:3',
            'low_stock_threshold' => 'decimal:3',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(IngredientCategory::class, 'ingredient_category_id');
    }

    /** Supplier-wise stock entries (stock IN). */
    public function stockEntries(): HasMany
    {
        return $this->hasMany(IngredientStockEntry::class);
    }

    /** Usage records (stock OUT). */
    public function usages(): HasMany
    {
        return $this->hasMany(IngredientUsage::class);
    }

    public function isLowStock(): bool
    {
        return $this->low_stock_threshold > 0
            && $this->available_quantity <= $this->low_stock_threshold;
    }

    /**
     * Format a quantity for display: trims trailing zeros without ever
     * mangling whole numbers (e.g. 300 stays "300", not "3"; 100.5 -> "100.5").
     */
    public static function formatQty($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    }

    /** Recalculate total available quantity = total purchased - total used. */
    public function recalculateQuantity(): void
    {
        $in = $this->stockEntries()->sum('quantity');
        $out = $this->usages()->sum('quantity');
        $this->available_quantity = $in - $out;
        $this->save();
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('available_quantity', '<=', 'low_stock_threshold')
            ->where('low_stock_threshold', '>', 0);
    }
}

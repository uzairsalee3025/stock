<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** Default threshold used by the optional low-stock report. */
    public const LOW_STOCK_THRESHOLD = 10;

    protected $fillable = [
        'product_category_id',
        'name',
        'quantity_stock',
    ];

    protected function casts(): array
    {
        return [
            'quantity_stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(ProductSale::class);
    }

    public function isLowStock(int $threshold = self::LOW_STOCK_THRESHOLD): bool
    {
        return $this->quantity_stock <= $threshold;
    }

    public function scopeLowStock(Builder $query, int $threshold = self::LOW_STOCK_THRESHOLD): Builder
    {
        return $query->where('quantity_stock', '<=', $threshold);
    }
}

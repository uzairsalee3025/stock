<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSale extends Model
{
    protected $fillable = [
        'product_id',
        'sale_quantity',
        'sale_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sale_quantity' => 'integer',
            'sale_date' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

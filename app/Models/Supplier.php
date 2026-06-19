<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'notes'];

    /**
     * Stock entries are linked by supplier name (free-text on the entry),
     * so this is a name-matched query rather than a foreign-key relation.
     */
    public function stockEntries(): Builder
    {
        return IngredientStockEntry::query()->where('supplier_name', $this->name);
    }
}

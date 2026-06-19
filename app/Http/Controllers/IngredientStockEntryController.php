<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientStockEntry;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientStockEntryController extends Controller
{
    /** Add another supplier's stock entry to an existing ingredient. */
    public function store(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($ingredient, $data) {
            $ingredient->stockEntries()->create($data);
            // Adding a supplier quantity increases the total available quantity.
            $ingredient->increment('available_quantity', $data['quantity']);
            Supplier::firstOrCreate(['name' => $data['supplier_name']]);
        });

        return redirect()->route('ingredients.show', $ingredient)
            ->with('success', "Added {$data['quantity']} {$ingredient->unit} from {$data['supplier_name']}.");
    }

    public function destroy(IngredientStockEntry $entry)
    {
        $ingredient = $entry->ingredient;

        DB::transaction(function () use ($entry, $ingredient) {
            // Reverse the quantity this entry had added.
            $ingredient->decrement('available_quantity', $entry->quantity);
            $entry->delete();
        });

        return redirect()->route('ingredients.show', $ingredient)
            ->with('success', 'Supplier entry removed and total quantity adjusted.');
    }
}

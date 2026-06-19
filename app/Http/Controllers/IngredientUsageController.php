<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\IngredientUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IngredientUsageController extends Controller
{
    /** Usage history (stock OUT), with optional filters. */
    public function index(Request $request)
    {
        $query = IngredientUsage::with('ingredient.category');

        if ($categoryId = $request->input('category')) {
            $query->whereHas('ingredient', fn ($q) => $q->where('ingredient_category_id', $categoryId));
        }
        if ($ingredientId = $request->input('ingredient')) {
            $query->where('ingredient_id', $ingredientId);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('usage_date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('usage_date', '<=', $to);
        }

        $usages = $query->latest('usage_date')->latest('id')->paginate(15)->withQueryString();
        $categories = IngredientCategory::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get(['id', 'name', 'ingredient_category_id']);

        return view('ingredient_usages.index', compact('usages', 'categories', 'ingredients'));
    }

    /** Form to record a usage: pick category -> ingredient, see available qty, deduct. */
    public function create()
    {
        return view('ingredient_usages.create', [
            'categories' => IngredientCategory::orderBy('name')->get(),
            // Used by JS to filter ingredients by category and show available quantity.
            'ingredients' => Ingredient::orderBy('name')->get(['id', 'name', 'unit', 'ingredient_category_id', 'available_quantity']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ingredient_id' => ['required', 'exists:ingredients,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'usage_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $ingredient = Ingredient::findOrFail($data['ingredient_id']);

        // Cannot use more than what is available.
        if ($data['quantity'] > $ingredient->available_quantity) {
            throw ValidationException::withMessages([
                'quantity' => "Only {$ingredient->available_quantity} {$ingredient->unit} available for {$ingredient->name}.",
            ]);
        }

        DB::transaction(function () use ($ingredient, $data) {
            $ingredient->usages()->create([
                'quantity' => $data['quantity'],
                'usage_date' => $data['usage_date'],
                'notes' => $data['notes'] ?? null,
            ]);
            // Deduct from total available quantity.
            $ingredient->decrement('available_quantity', $data['quantity']);
        });

        return redirect()->route('ingredient-usages.index')
            ->with('success', "Used {$data['quantity']} {$ingredient->unit} of {$ingredient->name}. Remaining: {$ingredient->fresh()->available_quantity} {$ingredient->unit}.");
    }

    public function destroy(IngredientUsage $ingredientUsage)
    {
        $ingredient = $ingredientUsage->ingredient;

        DB::transaction(function () use ($ingredientUsage, $ingredient) {
            // Reverse the usage — return the quantity to stock.
            $ingredient->increment('available_quantity', $ingredientUsage->quantity);
            $ingredientUsage->delete();
        });

        return redirect()->route('ingredient-usages.index')
            ->with('success', 'Usage entry removed and quantity returned to stock.');
    }
}

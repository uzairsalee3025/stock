<?php

namespace App\Http\Controllers;

use App\Models\IngredientCategory;
use Illuminate\Http\Request;

class IngredientCategoryController extends Controller
{
    public function index()
    {
        $categories = IngredientCategory::withCount('ingredients')
            ->orderBy('name')
            ->paginate(15);

        return view('ingredient_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('ingredient_categories.create');
    }

    public function store(Request $request)
    {
        IngredientCategory::create($this->validateData($request));

        return redirect()->route('ingredient-categories.index')
            ->with('success', 'Ingredient category created.');
    }

    public function edit(IngredientCategory $ingredientCategory)
    {
        return view('ingredient_categories.edit', ['category' => $ingredientCategory]);
    }

    public function update(Request $request, IngredientCategory $ingredientCategory)
    {
        $ingredientCategory->update($this->validateData($request, $ingredientCategory->id));

        return redirect()->route('ingredient-categories.index')
            ->with('success', 'Ingredient category updated.');
    }

    public function destroy(IngredientCategory $ingredientCategory)
    {
        if ($ingredientCategory->ingredients()->exists()) {
            return back()->with('error', 'Cannot delete a category that still has ingredients.');
        }

        $ingredientCategory->delete();

        return redirect()->route('ingredient-categories.index')
            ->with('success', 'Ingredient category deleted.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:ingredient_categories,name'.($ignoreId ? ",{$ignoreId}" : '')],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
    }
}

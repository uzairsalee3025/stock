<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::withCount('products')
            ->orderBy('name')
            ->paginate(15);

        return view('product_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('product_categories.create');
    }

    public function store(Request $request)
    {
        ProductCategory::create($this->validateData($request));

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category created.');
    }

    public function edit(ProductCategory $productCategory)
    {
        return view('product_categories.edit', ['category' => $productCategory]);
    }

    public function update(Request $request, ProductCategory $productCategory)
    {
        $productCategory->update($this->validateData($request, $productCategory->id));

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category updated.');
    }

    public function destroy(ProductCategory $productCategory)
    {
        if ($productCategory->products()->exists()) {
            return back()->with('error', 'Cannot delete a category that still has products.');
        }

        $productCategory->delete();

        return redirect()->route('product-categories.index')
            ->with('success', 'Product category deleted.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:product_categories,name'.($ignoreId ? ",{$ignoreId}" : '')],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
    }
}

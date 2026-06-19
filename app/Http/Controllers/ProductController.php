<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** Listing: category, name, available stock, created date, actions. */
    public function index(Request $request)
    {
        $query = Product::query()->with('category');

        if ($search = trim((string) $request->input('search'))) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId = $request->input('category')) {
            $query->where('product_category_id', $categoryId);
        }

        if ($request->input('stock') === 'low') {
            $query->lowStock();
        } elseif ($request->input('stock') === 'out') {
            $query->where('quantity_stock', '<=', 0);
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = ProductCategory::orderBy('name')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('products.create', [
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $product = Product::create($this->validateData($request));

        return redirect()->route('products.show', $product)
            ->with('success', 'Product added with stock.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'sales' => fn ($q) => $q->latest('sale_date')->latest('id')]);

        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', [
            'product' => $product,
            'categories' => ProductCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validateData($request));

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted along with its sale history.');
    }

    /** Only category, name and stock quantity are required. */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'quantity_stock' => ['required', 'integer', 'min:0'],
        ]);
    }
}

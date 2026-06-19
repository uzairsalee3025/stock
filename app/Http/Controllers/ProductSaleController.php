<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductSaleController extends Controller
{
    /** Sale / deduction history with optional filters. */
    public function index(Request $request)
    {
        $query = ProductSale::with('product.category');

        if ($categoryId = $request->input('category')) {
            $query->whereHas('product', fn ($q) => $q->where('product_category_id', $categoryId));
        }
        if ($productId = $request->input('product')) {
            $query->where('product_id', $productId);
        }
        if ($from = $request->input('from')) {
            $query->whereDate('sale_date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('sale_date', '<=', $to);
        }

        $sales = $query->latest('sale_date')->latest('id')->paginate(15)->withQueryString();
        $categories = ProductCategory::orderBy('name')->get();
        $products = Product::orderBy('name')->get(['id', 'name', 'product_category_id']);

        return view('product_sales.index', compact('sales', 'categories', 'products'));
    }

    /** Sell form: pick category -> product, see available stock, enter sale qty. */
    public function create()
    {
        return view('product_sales.create', [
            'categories' => ProductCategory::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(['id', 'name', 'product_category_id', 'quantity_stock']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'sale_quantity' => ['required', 'integer', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        // Sale quantity cannot exceed available stock.
        if ($data['sale_quantity'] > $product->quantity_stock) {
            throw ValidationException::withMessages([
                'sale_quantity' => "Only {$product->quantity_stock} in stock for {$product->name}.",
            ]);
        }

        DB::transaction(function () use ($product, $data) {
            $product->sales()->create([
                'sale_quantity' => $data['sale_quantity'],
                'sale_date' => now()->toDateString(),
                'notes' => $data['notes'] ?? null,
            ]);
            // Deduct sold quantity from available stock.
            $product->decrement('quantity_stock', $data['sale_quantity']);
        });

        return redirect()->route('product-sales.index')
            ->with('success', "Sold {$data['sale_quantity']} of {$product->name}. Remaining: {$product->fresh()->quantity_stock}.");
    }

    public function destroy(ProductSale $productSale)
    {
        $product = $productSale->product;

        DB::transaction(function () use ($productSale, $product) {
            // Reverse the sale — return quantity to stock.
            $product->increment('quantity_stock', $productSale->sale_quantity);
            $productSale->delete();
        });

        return redirect()->route('product-sales.index')
            ->with('success', 'Sale removed and stock returned.');
    }
}

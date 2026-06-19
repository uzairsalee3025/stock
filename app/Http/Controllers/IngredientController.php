<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngredientController extends Controller
{
    /** List + search/filter by category, name, supplier, date, stock status. */
    public function index(Request $request)
    {
        $query = Ingredient::query()->with('category');

        if ($search = trim((string) $request->input('search'))) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categoryId = $request->input('category')) {
            $query->where('ingredient_category_id', $categoryId);
        }

        // Filter by supplier name or stock-entry date.
        $supplier = trim((string) $request->input('supplier'));
        $date = $request->input('date');
        if ($supplier || $date) {
            $query->whereHas('stockEntries', function ($q) use ($supplier, $date) {
                if ($supplier) {
                    $q->where('supplier_name', 'like', "%{$supplier}%");
                }
                if ($date) {
                    $q->whereDate('date', $date);
                }
            });
        }

        if ($request->input('stock') === 'low') {
            $query->lowStock();
        } elseif ($request->input('stock') === 'out') {
            $query->where('available_quantity', '<=', 0);
        }

        $ingredients = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = IngredientCategory::orderBy('name')->get();
        $supplierNames = Supplier::orderBy('name')->pluck('name');

        return view('ingredients.index', compact('ingredients', 'categories', 'supplierNames'));
    }

    public function create()
    {
        return view('ingredients.create', [
            'categories' => IngredientCategory::orderBy('name')->get(),
            'supplierNames' => Supplier::orderBy('name')->pluck('name'),
        ]);
    }

    /**
     * Create an ingredient together with its first supplier stock entry.
     * If the same ingredient (same category + name) already exists, the new
     * supplier quantity is simply added to that ingredient's total.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'ingredient_category_id' => ['required', 'exists:ingredient_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'supplier_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $ingredient = DB::transaction(function () use ($data) {
            $ingredient = Ingredient::firstOrNew([
                'ingredient_category_id' => $data['ingredient_category_id'],
                'name' => $data['name'],
            ]);

            if (! $ingredient->exists) {
                $ingredient->unit = $data['unit'];
                $ingredient->available_quantity = 0;
                $ingredient->low_stock_threshold = 0;
                $ingredient->notes = $data['notes'] ?? null;
                $ingredient->save();
            }

            // Supplier-wise entry.
            $ingredient->stockEntries()->create([
                'supplier_name' => $data['supplier_name'],
                'quantity' => $data['quantity'],
                'price' => $data['price'],
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
            ]);

            // Total available quantity grows by this supplier's quantity.
            $ingredient->increment('available_quantity', $data['quantity']);

            // Keep the managed supplier list in sync for dropdowns/reports.
            Supplier::firstOrCreate(['name' => $data['supplier_name']]);

            return $ingredient;
        });

        return redirect()->route('ingredients.show', $ingredient)
            ->with('success', "Stock added for {$ingredient->name} from {$data['supplier_name']}.");
    }

    public function show(Ingredient $ingredient)
    {
        $ingredient->load([
            'category',
            'stockEntries' => fn ($q) => $q->latest('date'),
            'usages' => fn ($q) => $q->latest('usage_date'),
        ]);

        // Quantity totals — SUM of quantity, never record counts.
        $purchaseTotal = (float) $ingredient->stockEntries()->sum('quantity');
        $usedTotal = (float) $ingredient->usages()->sum('quantity');
        $availableTotal = $purchaseTotal - $usedTotal;

        // Supplier-wise breakdown of available quantity (the Rice example).
        $supplierBreakdown = $ingredient->stockEntries()
            ->select('supplier_name')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(quantity * price) as total_value')
            ->groupBy('supplier_name')
            ->orderBy('supplier_name')
            ->get();

        $supplierTotalQuantity = (float) $supplierBreakdown->sum('total_quantity');
        $supplierTotalValue = (float) $supplierBreakdown->sum('total_value');

        $supplierNames = Supplier::orderBy('name')->pluck('name');

        return view('ingredients.show', compact(
            'ingredient', 'supplierBreakdown', 'supplierNames',
            'purchaseTotal', 'usedTotal', 'availableTotal',
            'supplierTotalQuantity', 'supplierTotalValue'
        ));
    }

    public function edit(Ingredient $ingredient)
    {
        return view('ingredients.edit', [
            'ingredient' => $ingredient,
            'categories' => IngredientCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $data = $request->validate([
            'ingredient_category_id' => ['required', 'exists:ingredient_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:20'],
            'low_stock_threshold' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        $data['low_stock_threshold'] = $data['low_stock_threshold'] ?? 0;

        $ingredient->update($data);

        return redirect()->route('ingredients.show', $ingredient)
            ->with('success', 'Ingredient updated.');
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return redirect()->route('ingredients.index')
            ->with('success', 'Ingredient deleted along with its stock history.');
    }
}

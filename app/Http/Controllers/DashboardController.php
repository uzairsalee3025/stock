<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientCategory;
use App\Models\IngredientStockEntry;
use App\Models\IngredientUsage;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSale;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $today = now()->toDateString();
        $data = [];

        // ---- Patient summary (admin + staff) ----
        if ($user->canManagePatients()) {
            $data += [
                'totalPatients' => Patient::count(),
                'todaysPatients' => PatientVisit::whereDate('visit_date', $today)
                    ->distinct('patient_id')->count('patient_id'),
                'recentPatients' => Patient::with('latestVisit')->latest()->take(5)->get(),
                'upcomingFollowUps' => PatientVisit::with('patient')
                    ->whereNotNull('follow_up_date')
                    ->whereDate('follow_up_date', '>=', $today)
                    ->orderBy('follow_up_date')
                    ->take(5)->get(),
                'latestVisits' => PatientVisit::with('patient')
                    ->latest('visit_date')->latest('id')->take(5)->get(),
            ];
        }

        // ---- Ingredient inventory summary (admin + inventory) ----
        if ($user->canManageInventory()) {
            $data += [
                'totalIngredients' => Ingredient::count(),
                'totalIngredientCategories' => IngredientCategory::count(),
                'lowStockIngredients' => Ingredient::with('category')->lowStock()->orderBy('available_quantity')->get(),
                'recentStockEntries' => IngredientStockEntry::with('ingredient')
                    ->latest('date')->latest('id')->take(6)->get(),
                'recentUsages' => IngredientUsage::with('ingredient')
                    ->latest('usage_date')->latest('id')->take(6)->get(),

                // ---- Product summary (admin + inventory) ----
                'totalProducts' => Product::count(),
                'totalProductCategories' => ProductCategory::count(),
                'lowStockProducts' => Product::with('category')->lowStock()->orderBy('quantity_stock')->get(),
                'recentSales' => ProductSale::with('product')
                    ->latest('sale_date')->latest('id')->take(6)->get(),
                'topStockProducts' => Product::with('category')->orderByDesc('quantity_stock')->take(6)->get(),
            ];
        }

        return view('dashboard', $data);
    }
}

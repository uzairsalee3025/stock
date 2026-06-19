<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\IngredientStockEntry;
use App\Models\IngredientUsage;
use App\Models\Patient;
use App\Models\PatientVisit;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /* ---------------- Patient reports ---------------- */
    public function patients(Request $request)
    {
        $report = $request->input('report', 'date_wise');
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $rows = collect();

        if ($report === 'serial') {
            // Serial number search report.
            if ($serial = trim((string) $request->input('serial'))) {
                $rows = Patient::with('visits')
                    ->where('serial_number', 'like', "%{$serial}%")
                    ->get();
            }
        } elseif ($report === 'follow_up') {
            // Follow-up patient report.
            $rows = PatientVisit::with('patient')
                ->whereNotNull('follow_up_date')
                ->whereBetween('follow_up_date', [$from, $to])
                ->orderBy('follow_up_date')
                ->get();
        } elseif ($report === 'prescription') {
            // Prescription upload report.
            $rows = PatientVisit::with('patient')
                ->whereNotNull('prescription_path')
                ->whereBetween('visit_date', [$from, $to])
                ->orderByDesc('visit_date')
                ->get();
        } else {
            // Date-wise patient (visits) report.
            $rows = PatientVisit::with('patient')
                ->whereBetween('visit_date', [$from, $to])
                ->orderByDesc('visit_date')
                ->get();
        }

        return view('reports.patients', compact('report', 'from', 'to', 'rows'));
    }

    /* ---------------- Ingredient reports ---------------- */
    public function ingredients(Request $request)
    {
        $report = $request->input('report', 'available');
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $rows = collect();
        $supplierNames = Supplier::orderBy('name')->pluck('name');
        $supplier = trim((string) $request->input('supplier'));

        if ($report === 'supplier_wise') {
            $rows = IngredientStockEntry::with('ingredient')
                ->when($supplier, fn ($q) => $q->where('supplier_name', $supplier))
                ->whereBetween('date', [$from, $to])
                ->orderBy('supplier_name')
                ->orderByDesc('date')
                ->get();
        } elseif ($report === 'purchase') {
            $rows = IngredientStockEntry::with('ingredient')
                ->whereBetween('date', [$from, $to])
                ->orderByDesc('date')
                ->get();
        } elseif ($report === 'usage') {
            $rows = IngredientUsage::with('ingredient')
                ->whereBetween('usage_date', [$from, $to])
                ->orderByDesc('usage_date')
                ->get();
        } elseif ($report === 'low_stock') {
            $rows = Ingredient::with('category')->lowStock()->orderBy('name')->get();
        } else { // available stock
            $rows = Ingredient::with('category')->orderBy('name')->get();
        }

        return view('reports.ingredients', compact('report', 'from', 'to', 'rows', 'supplierNames', 'supplier'));
    }

    /* ---------------- Product reports (simple) ---------------- */
    public function products(Request $request)
    {
        $report = $request->input('report', 'available');

        if ($report === 'low_stock') {
            $rows = Product::with('category')->lowStock()->orderBy('quantity_stock')->get();
        } elseif ($report === 'sold') {
            // Sold quantity report: total sold per product.
            $rows = Product::with('category')
                ->withSum('sales as total_sold', 'sale_quantity')
                ->orderByDesc('total_sold')
                ->get();
        } else {
            // Available stock report (also serves "remaining").
            $rows = Product::with('category')->orderBy('name')->get();
        }

        return view('reports.products', compact('report', 'rows'));
    }
}

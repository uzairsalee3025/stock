<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientCategoryController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\IngredientStockEntryController;
use App\Http\Controllers\IngredientUsageController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientVisitController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSaleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/* ---------------- Authentication ---------------- */
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Dashboard — accessible to every signed-in user.
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile — every signed-in user can change their own password.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    /* ============ Module 1: Patient History (admin + staff) ============ */
    Route::middleware('role:staff')->group(function () {
        Route::resource('patients', PatientController::class);

        // Repeat / follow-up visits + slip uploads.
        Route::post('patients/{patient}/visits', [PatientVisitController::class, 'store'])->name('patients.visits.store');
        Route::get('visits/{visit}/edit', [PatientVisitController::class, 'edit'])->name('visits.edit');
        Route::put('visits/{visit}', [PatientVisitController::class, 'update'])->name('visits.update');
        Route::delete('visits/{visit}', [PatientVisitController::class, 'destroy'])->name('visits.destroy');

        Route::get('reports/patients', [ReportController::class, 'patients'])->name('reports.patients');
    });

    /* ============ Modules 2 & 3: Inventory (admin + inventory) ============ */
    Route::middleware('role:inventory')->group(function () {
        // Ingredient inventory
        Route::resource('ingredient-categories', IngredientCategoryController::class)->except('show');
        Route::resource('suppliers', SupplierController::class);
        Route::resource('ingredients', IngredientController::class);

        // Supplier-wise stock entries (add stock to an existing ingredient).
        Route::post('ingredients/{ingredient}/entries', [IngredientStockEntryController::class, 'store'])->name('ingredients.entries.store');
        Route::delete('entries/{entry}', [IngredientStockEntryController::class, 'destroy'])->name('entries.destroy');

        // Ingredient Usage (deduct stock) — its own section.
        Route::resource('ingredient-usages', IngredientUsageController::class)->only(['index', 'create', 'store', 'destroy']);

        Route::get('reports/ingredients', [ReportController::class, 'ingredients'])->name('reports.ingredients');

        // Simple product management (kept separate from ingredients)
        Route::resource('product-categories', ProductCategoryController::class)->except('show');
        Route::resource('products', ProductController::class);

        // Product Sales (deduct stock) — its own section.
        Route::resource('product-sales', ProductSaleController::class)->only(['index', 'create', 'store', 'destroy']);

        Route::get('reports/products', [ReportController::class, 'products'])->name('reports.products');
    });
});

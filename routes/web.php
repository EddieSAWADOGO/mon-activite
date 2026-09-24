<?php

use App\Domain\Achats\Http\Controllers\PurchaseController;
use App\Domain\Fournisseurs\Http\Controllers\SupplierController;
use App\Domain\Produits\Http\Controllers\ProductController;
use App\Domain\Stock\Http\Controllers\StockController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('produits.index');
});

Route::middleware(['auth'])->group(function () {
    // Products
    Route::resource('produits', ProductController::class)->parameters([
        'produits' => 'product',
    ])->names([
        'index' => 'produits.index',
        'create' => 'produits.create',
        'store' => 'produits.store',
        'show' => 'produits.show',
        'edit' => 'produits.edit',
        'update' => 'produits.update',
        'destroy' => 'produits.destroy',
    ]);

    Route::patch('produits/{product}/unites/{stockUnit}/toggle-status', [ProductController::class, 'toggleUnitStatus'])
        ->name('produits.unites.toggle-status');

    // Suppliers
    Route::resource('fournisseurs', SupplierController::class)->parameters([
        'fournisseurs' => 'supplier',
    ])->names([
        'index' => 'fournisseurs.index',
        'create' => 'fournisseurs.create',
        'store' => 'fournisseurs.store',
        'show' => 'fournisseurs.show',
        'edit' => 'fournisseurs.edit',
        'update' => 'fournisseurs.update',
        'destroy' => 'fournisseurs.destroy',
    ]);

    // Purchases
    Route::resource('achats', PurchaseController::class)->only(['index', 'create', 'store', 'show'])->parameters([
        'achats' => 'purchase',
    ])->names([
        'index' => 'achats.index',
        'create' => 'achats.create',
        'store' => 'achats.store',
        'show' => 'achats.show',
    ]);

    // Stock Module
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/mouvements', [StockController::class, 'movements'])->name('stock.movements');
});

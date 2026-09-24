<?php

use App\Domain\Achats\Http\Controllers\PurchaseController;
use App\Domain\Clients\Http\Controllers\CustomerController;
use App\Domain\Facturation\Http\Controllers\InvoiceController;
use App\Domain\Fournisseurs\Http\Controllers\SupplierController;
use App\Domain\Paiements\Http\Controllers\PaymentController;
use App\Domain\Produits\Http\Controllers\ProductController;
use App\Domain\Stock\Http\Controllers\StockController;
use App\Domain\Ventes\Http\Controllers\SaleController;
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

    // Customers
    Route::resource('clients', CustomerController::class)->parameters([
        'clients' => 'customer',
    ])->names([
        'index' => 'clients.index',
        'create' => 'clients.create',
        'store' => 'clients.store',
        'show' => 'clients.show',
        'edit' => 'clients.edit',
        'update' => 'clients.update',
        'destroy' => 'clients.destroy',
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

    // Sales
    Route::resource('ventes', SaleController::class)->only(['index', 'create', 'store', 'show'])->parameters([
        'ventes' => 'sale',
    ])->names([
        'index' => 'ventes.index',
        'create' => 'ventes.create',
        'store' => 'ventes.store',
        'show' => 'ventes.show',
    ]);

    // Invoices
    Route::resource('factures', InvoiceController::class)->only(['index', 'show'])->parameters([
        'factures' => 'invoice',
    ])->names([
        'index' => 'factures.index',
        'show' => 'factures.show',
    ]);

    Route::get('factures/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('factures.pdf');
    Route::get('factures/{invoice}/whatsapp', [InvoiceController::class, 'whatsapp'])->name('factures.whatsapp');

    // Payments
    Route::get('paiements', [PaymentController::class, 'index'])->name('paiements.index');
    Route::get('factures/{invoice}/regler', [PaymentController::class, 'create'])->name('paiements.create');
    Route::post('paiements', [PaymentController::class, 'store'])->name('paiements.store');

    // Stock Module
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/mouvements', [StockController::class, 'movements'])->name('stock.movements');
});

<?php

use App\Domain\Produits\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('produits.index');
});

Route::middleware(['auth'])->group(function () {
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
});

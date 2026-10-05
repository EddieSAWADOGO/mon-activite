<?php

namespace App\Domain\Produits\Http\Controllers;

use App\Domain\Produits\Http\Requests\StoreProductRequest;
use App\Domain\Produits\Http\Requests\UpdateProductRequest;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Produits\Services\ProductService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Exception;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()->with(['units', 'baseUnit']);

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'active') {
                $query->where('is_active', true);
            } elseif ($request->input('status') === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->boolean('low_stock')) {
            $query->lowStock();
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('produits.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $this->authorize('create', Product::class);

        return view('produits.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(StoreProductRequest $request, ProductService $productService): RedirectResponse
    {
        $product = $productService->createProduct($request->validated());

        return redirect()
            ->route('produits.show', $product)
            ->with('success', "Le produit '{$product->name}' a été créé avec succès.");
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $this->authorize('view', $product);

        $product->load([
            'units' => fn ($q) => $q->orderBy('is_base_unit', 'desc')->orderBy('created_at', 'asc'),
            'movements' => fn ($q) => $q->with(['stockUnit', 'createdBy'])->latest()->take(20)
        ]);

        return view('produits.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $this->authorize('update', $product);

        $product->load(['units' => fn ($q) => $q->orderBy('is_base_unit', 'desc')->orderBy('created_at', 'asc')]);

        return view('produits.edit', compact('product'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(UpdateProductRequest $request, Product $product, ProductService $productService): RedirectResponse
    {
        $product = $productService->updateProduct($product, $request->validated());

        return redirect()
            ->route('produits.show', $product)
            ->with('success', "Le produit '{$product->name}' a été mis à jour avec succès.");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product, ProductService $productService): RedirectResponse
    {
        $this->authorize('delete', $product);

        try {
            $productName = $product->name;
            $productService->deleteProduct($product);

            return redirect()
                ->route('produits.index')
                ->with('success', "Le produit '{$productName}' a été supprimé.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle the active/archived status of a stock unit.
     */
    public function toggleUnitStatus(Product $product, StockUnit $stockUnit, ProductService $productService): RedirectResponse
    {
        $this->authorize('update', $product);

        try {
            $stockUnit = $productService->toggleUnitStatus($product, $stockUnit);
            $statusText = $stockUnit->is_active ? 'activée' : 'archivée';

            return redirect()
                ->back()
                ->with('success', "L'unité '{$stockUnit->name}' a été {$statusText}.");
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}

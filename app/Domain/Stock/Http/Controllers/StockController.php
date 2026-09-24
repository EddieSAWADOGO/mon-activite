<?php

namespace App\Domain\Stock\Http\Controllers;

use App\Domain\Produits\Models\Product;
use App\Domain\Stock\Models\StockMovement;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Overview of stock per product and per unit.
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)
            ->with(['activeUnits' => function ($q) {
                $q->orderByDesc('is_base_unit');
            }]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('low_stock')) {
            $query->whereHas('activeUnits', function ($uq) {
                $uq->whereRaw('current_stock <= low_stock_threshold');
            });
        }

        $products = $query->paginate(15)->withQueryString();

        return view('stock.index', compact('products'));
    }

    /**
     * Global history of stock movements.
     */
    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'stockUnit', 'createdBy'])
            ->latest('movement_date');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->input('product_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('movement_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('movement_date', '<=', $request->input('end_date'));
        }

        $movements = $query->paginate(20)->withQueryString();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('stock.movements', compact('movements', 'products'));
    }
}

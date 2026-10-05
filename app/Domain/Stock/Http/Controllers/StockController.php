<?php

namespace App\Domain\Stock\Http\Controllers;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Http\Requests\StoreInventoryRequest;
use App\Domain\Stock\Models\StockMovement;
use App\Domain\Stock\Services\StockMovementService;
use App\Http\Controllers\Controller;
use App\Support\Enums\MovementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->latest('movement_date')
            ->latest('id');

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

    /**
     * Form to record physical inventory count and adjust stock.
     */
    public function createInventory(Request $request)
    {
        if (! $request->user()->canManageInventoryOperations()) {
            abort(403, "Vous n'avez pas l'autorisation d'effectuer des ajustements d'inventaire.");
        }

        $products = Product::where('is_active', true)
            ->with(['activeUnits'])
            ->orderBy('name')
            ->get();

        $selectedProductId = $request->input('product_id');
        $selectedUnitId = $request->input('stock_unit_id');

        return view('stock.inventory', compact('products', 'selectedProductId', 'selectedUnitId'));
    }

    /**
     * Save physical inventory adjustment.
     */
    public function storeInventory(StoreInventoryRequest $request)
    {
        $data = $request->validated();

        $stockUnit = StockUnit::findOrFail($data['stock_unit_id']);
        $systemQty = (float) $stockUnit->current_stock;
        $physicalQty = (float) $data['physical_quantity'];
        $gap = $physicalQty - $systemQty;

        if (abs($gap) < 0.0001) {
            return redirect()->route('stock.index')
                ->with('info', "L'inventaire pour '{$stockUnit->name}' est conforme (Stock système : {$systemQty}). Aucun ajustement n'a été appliqué.");
        }

        $direction = $gap > 0 ? 'in' : 'out';
        $absGap = abs($gap);
        $gapText = $gap > 0 ? "+{$absGap}" : "-{$absGap}";

        $note = "Inventaire physique : {$physicalQty} au lieu de {$systemQty} en système (Écart : {$gapText}). " . ($data['notes'] ?? '');

        DB::transaction(function () use ($stockUnit, $physicalQty, $data, $direction, $absGap, $note, $request) {
            $stockUnit->current_stock = $physicalQty;
            $stockUnit->save();

            StockMovement::create([
                'product_id' => $data['product_id'],
                'stock_unit_id' => $data['stock_unit_id'],
                'type' => MovementType::INVENTORY_ADJUSTMENT,
                'quantity' => $absGap,
                'direction' => $direction,
                'created_by_user_id' => $request->user()->id,
                'movement_date' => $data['inventory_date'],
                'notes' => trim($note),
            ]);
        });

        return redirect()->route('stock.index')
            ->with('success', "Ajustement d'inventaire enregistré avec succès pour '{$stockUnit->name}' (Nouveau stock : {$physicalQty}).");
    }
}

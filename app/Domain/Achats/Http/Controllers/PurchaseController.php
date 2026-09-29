<?php

namespace App\Domain\Achats\Http\Controllers;

use App\Domain\Achats\Http\Requests\StorePurchaseRequest;
use App\Domain\Achats\Models\Purchase;
use App\Domain\Achats\Services\PurchaseService;
use App\Domain\Fournisseurs\Models\Supplier;
use App\Domain\Produits\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchaseService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Purchase::class);

        $query = Purchase::with(['supplier', 'createdBy', 'lines.product'])
            ->latest('purchase_date');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('purchase_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('purchase_date', '<=', $request->input('end_date'));
        }

        $purchases = $query->paginate(15)->withQueryString();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        return view('achats.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $this->authorize('create', Purchase::class);

        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->with(['activeUnits'])
            ->orderBy('name')
            ->get();

        return view('achats.create', compact('suppliers', 'products'));
    }

    public function store(StorePurchaseRequest $request)
    {
        $purchase = $this->purchaseService->registerPurchase(
            $request->validated(),
            $request->user()->id
        );

        return redirect()->route('achats.show', $purchase)
            ->with('success', "L'achat n° {$purchase->purchase_number} a été enregistré avec succès.");
    }

    public function show(Purchase $purchase)
    {
        $this->authorize('view', $purchase);

        $purchase->load(['supplier', 'createdBy', 'lines.product', 'lines.stockUnit', 'payments.createdBy']);

        return view('achats.show', compact('purchase'));
    }
}

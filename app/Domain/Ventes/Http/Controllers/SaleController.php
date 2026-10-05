<?php

namespace App\Domain\Ventes\Http\Controllers;

use App\Domain\Clients\Models\Customer;
use App\Domain\Produits\Models\Product;
use App\Domain\Ventes\Http\Requests\StoreSaleRequest;
use App\Domain\Ventes\Models\Sale;
use App\Domain\Ventes\Services\SaleService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Sale::class);

        $query = Sale::with(['customer', 'createdBy', 'invoice'])->latest('sale_date')->latest('id');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('sale_date', '>=', $startDate);
        }

        if ($endDate = $request->input('end_date')) {
            $query->whereDate('sale_date', '<=', $endDate);
        }

        $sales = $query->paginate(15)->withQueryString();

        return view('ventes.index', compact('sales'));
    }

    public function create(): View
    {
        $this->authorize('create', Sale::class);

        $products = Product::where('is_active', true)
            ->with(['activeUnits' => function ($query) {
                $query->orderBy('is_base_unit', 'desc');
            }])
            ->orderBy('name')
            ->get();

        $customers = Customer::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('ventes.create', compact('products', 'customers'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $sale = $this->saleService->createSale($request->validated(), $request->user());

        return redirect()->route('factures.show', $sale->invoice)
            ->with('success', "Vente N° {$sale->sale_number} enregistrée et facture émise.");
    }

    public function show(Sale $sale): View
    {
        $this->authorize('view', $sale);

        $sale->load(['customer', 'createdBy', 'lines.product', 'lines.stockUnit', 'lines.sourceStockUnit', 'invoice']);

        return view('ventes.show', compact('sale'));
    }
}

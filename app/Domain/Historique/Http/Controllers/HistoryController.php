<?php

namespace App\Domain\Historique\Http\Controllers;

use App\Domain\Achats\Models\PurchaseLine;
use App\Domain\Clients\Models\Customer;
use App\Domain\Fournisseurs\Models\Supplier;
use App\Domain\Produits\Models\Product;
use App\Domain\Stock\Services\StockSnapshotService;
use App\Domain\Ventes\Models\SaleLine;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', 'history');

        return view('historique.index');
    }

    public function purchases(Request $request): View
    {
        $this->authorize('viewAny', 'history');

        $products = Product::orderBy('name')->get();
        $selectedProductId = $request->input('product_id');
        $period = $request->input('period', 'month');

        [$startDate, $endDate] = $this->resolveDateRange($period, $request->input('start_date'), $request->input('end_date'));

        $query = PurchaseLine::with(['purchase.supplier', 'product', 'stockUnit'])
            ->whereHas('purchase', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('purchase_date', [$startDate, $endDate]);
            });

        if ($selectedProductId) {
            $query->where('product_id', $selectedProductId);
        }

        $lines = $query->latest()->paginate(20)->withQueryString();
        $totalAmount = (float) (clone $query)->get()->sum('subtotal');

        return view('historique.purchases', compact('products', 'selectedProductId', 'period', 'startDate', 'endDate', 'lines', 'totalAmount'));
    }

    public function sales(Request $request): View
    {
        $this->authorize('viewAny', 'history');

        $products = Product::orderBy('name')->get();
        $selectedProductId = $request->input('product_id');
        $period = $request->input('period', 'month');

        [$startDate, $endDate] = $this->resolveDateRange($period, $request->input('start_date'), $request->input('end_date'));

        $query = SaleLine::with(['sale.customer', 'product', 'stockUnit'])
            ->whereHas('sale', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('sale_date', [$startDate, $endDate]);
            });

        if ($selectedProductId) {
            $query->where('product_id', $selectedProductId);
        }

        $lines = $query->latest()->paginate(20)->withQueryString();
        $totalAmount = (float) (clone $query)->get()->sum('subtotal');

        return view('historique.sales', compact('products', 'selectedProductId', 'period', 'startDate', 'endDate', 'lines', 'totalAmount'));
    }

    public function stockAtDate(Request $request, StockSnapshotService $snapshotService): View
    {
        $this->authorize('viewAny', 'history');

        $products = Product::with('units')->orderBy('name')->get();
        $selectedProductId = $request->input('product_id');
        $targetDateInput = $request->input('date', now()->format('Y-m-d'));
        $targetDate = Carbon::parse($targetDateInput)->endOfDay();

        $calculatedStock = null;
        $selectedProduct = null;

        if ($selectedProductId) {
            $selectedProduct = Product::with('units')->find($selectedProductId);
            if ($selectedProduct) {
                $calculatedStock = $snapshotService->computeStockAtDate($selectedProduct, $targetDate);
            }
        }

        return view('historique.stock-at-date', compact('products', 'selectedProduct', 'selectedProductId', 'targetDateInput', 'calculatedStock'));
    }

    public function topProducts(Request $request): View
    {
        $this->authorize('viewAny', 'history');

        $period = $request->input('period', 'month');
        $sortBy = $request->input('sort_by', 'amount'); // amount or quantity

        [$startDate, $endDate] = $this->resolveDateRange($period, $request->input('start_date'), $request->input('end_date'));

        $query = DB::table('sale_lines')
            ->join('sales', 'sale_lines.sale_id', '=', 'sales.id')
            ->join('products', 'sale_lines.product_id', '=', 'products.id')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(sale_lines.quantity) as total_quantity'),
                DB::raw('SUM(sale_lines.subtotal) as total_amount')
            )
            ->groupBy('products.id', 'products.name');

        if ($sortBy === 'quantity') {
            $query->orderByDesc('total_quantity');
        } else {
            $query->orderByDesc('total_amount');
        }

        $topProducts = $query->limit(20)->get();

        return view('historique.top-products', compact('period', 'sortBy', 'startDate', 'endDate', 'topProducts'));
    }

    public function financialOverview(): View
    {
        $this->authorize('viewAny', 'history');

        $suppliers = Supplier::withSum('purchases as total_remaining', 'remaining_amount')
            ->withSum('purchases as total_purchased', 'total_amount')
            ->withSum('purchases as total_paid', 'paid_amount')
            ->where('is_active', true)
            ->get();

        $totalSupplierDebt = $suppliers->sum('total_remaining');

        $customers = Customer::withSum('sales as total_remaining', 'remaining_amount')
            ->withSum('sales as total_purchased', 'total_amount')
            ->withSum('sales as total_paid', 'paid_amount')
            ->where('is_active', true)
            ->get();

        $totalCustomerReceivables = $customers->sum('total_remaining');

        return view('historique.financial-overview', compact('suppliers', 'totalSupplierDebt', 'customers', 'totalCustomerReceivables'));
    }

    private function resolveDateRange(string $period, ?string $start = null, ?string $end = null): array
    {
        if ($period === 'custom' && $start && $end) {
            return [Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay()];
        }

        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'custom' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
}

<?php

namespace App\Domain\Utilisateurs\Http\Controllers;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Facturation\Models\Invoice;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Models\StockMovement;
use App\Domain\Ventes\Models\Sale;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();

        // Stock alerts (low stock)
        $lowStockUnits = StockUnit::with('product')
            ->where('is_active', true)
            ->whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->orderBy('current_stock', 'asc')
            ->limit(10)
            ->get();

        // Recent Invoices
        $recentInvoices = Invoice::with(['customer', 'createdBy'])
            ->latest('invoice_date')
            ->limit(5)
            ->get();

        // Today's Sales statistics
        $todaySalesQuery = Sale::whereDate('sale_date', now()->toDateString());
        $todaySalesCount = (int) $todaySalesQuery->count();
        $todaySalesTotal = (float) $todaySalesQuery->sum('total_amount');

        // Supplier debts & Customer receivables (only if admin/superadmin)
        $supplierTotalDebt = 0;
        $customerTotalReceivables = 0;

        if ($user->isAdmin()) {
            $supplierTotalDebt = (float) Purchase::sum('remaining_amount');
            $customerTotalReceivables = (float) Sale::sum('remaining_amount');
        }

        // Recent Stock Movements / Operations
        $recentMovements = StockMovement::with(['product', 'stockUnit', 'createdBy'])
            ->latest('movement_date')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'user',
            'lowStockUnits',
            'recentInvoices',
            'todaySalesCount',
            'todaySalesTotal',
            'supplierTotalDebt',
            'customerTotalReceivables',
            'recentMovements'
        ));
    }
}

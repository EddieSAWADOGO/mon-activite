<?php

namespace App\Domain\Achats\Services;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Achats\Models\PurchaseLine;
use App\Domain\Stock\Services\StockMovementService;
use App\Support\Enums\MovementType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseService
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    /**
     * Register a new purchase with multiple lines and update stock.
     */
    public function registerPurchase(array $data, int $userId): Purchase
    {
        return DB::transaction(function () use ($data, $userId) {
            $totalAmount = 0;
            $linesToProcess = [];

            foreach ($data['lines'] as $lineData) {
                $subtotal = (int) round($lineData['quantity'] * $lineData['unit_price']);
                $totalAmount += $subtotal;

                $linesToProcess[] = [
                    'product_id' => (int) $lineData['product_id'],
                    'stock_unit_id' => (int) $lineData['stock_unit_id'],
                    'quantity' => (float) $lineData['quantity'],
                    'unit_price' => (int) $lineData['unit_price'],
                    'subtotal' => $subtotal,
                ];
            }

            $paidAmount = isset($data['paid_amount']) ? (int) $data['paid_amount'] : $totalAmount;
            $remainingAmount = max(0, $totalAmount - $paidAmount);
            $purchaseDate = $data['purchase_date'] ?? now()->toDateString();

            // Generate unique purchase number ACH-YYYYMMDD-XXXX
            $datePrefix = date('Ymd', strtotime($purchaseDate));
            $count = Purchase::whereDate('created_at', now()->today())->count() + 1;
            $purchaseNumber = sprintf('ACH-%s-%04d', $datePrefix, $count);
            while (Purchase::where('purchase_number', $purchaseNumber)->exists()) {
                $count++;
                $purchaseNumber = sprintf('ACH-%s-%04d', $datePrefix, $count);
            }

            $purchase = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'supplier_id' => (int) $data['supplier_id'],
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'purchase_date' => $purchaseDate,
                'created_by_user_id' => $userId,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($linesToProcess as $lineData) {
                $purchaseLine = $purchase->lines()->create($lineData);

                // Record stock movement (IN)
                $this->stockMovementService->recordMovement(
                    productId: $lineData['product_id'],
                    stockUnitId: $lineData['stock_unit_id'],
                    type: MovementType::PURCHASE,
                    quantity: $lineData['quantity'],
                    direction: 'in',
                    reference: $purchaseLine,
                    userId: $userId,
                    movementDate: new \DateTime($purchaseDate),
                    notes: "Achat n° {$purchaseNumber}"
                );
            }

            return $purchase->load(['supplier', 'createdBy', 'lines.product', 'lines.stockUnit']);
        });
    }
}

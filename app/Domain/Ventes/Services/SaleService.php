<?php

namespace App\Domain\Ventes\Services;

use App\Domain\Facturation\Models\Invoice;
use App\Domain\Facturation\Models\InvoiceLine;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Services\StockMovementService;
use App\Domain\Ventes\Models\Sale;
use App\Domain\Ventes\Models\SaleLine;
use App\Models\User;
use App\Support\Enums\InvoiceStatus;
use App\Support\Enums\MovementType;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    public function createSale(array $data, User $user): Sale
    {
        return DB::transaction(function () use ($data, $user) {
            $saleNumber = $this->generateSaleNumber();
            $saleDate = $data['sale_date'] ?? now();

            // Calculate totals from validated lines
            $totalAmount = 0;
            $linesToCreate = [];

            foreach ($data['lines'] as $lineData) {
                $stockUnit = StockUnit::findOrFail($lineData['stock_unit_id']);
                $quantity = (float) $lineData['quantity'];
                $unitPrice = (float) $lineData['unit_price'];
                $defaultPrice = (float) $stockUnit->default_selling_price;
                $subtotal = $quantity * $unitPrice;
                $totalAmount += $subtotal;

                $linesToCreate[] = [
                    'product_id' => $lineData['product_id'],
                    'stock_unit_id' => $stockUnit->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'default_unit_price' => $defaultPrice,
                    'discount_reason' => $lineData['discount_reason'] ?? null,
                    'subtotal' => $subtotal,
                    'source_stock_unit_id' => $lineData['source_stock_unit_id'] ?? null,
                ];
            }

            $paidAmount = min((float) ($data['paid_amount'] ?? 0), $totalAmount);
            $remainingAmount = max(0, $totalAmount - $paidAmount);

            // Create Sale record
            $sale = Sale::create([
                'sale_number' => $saleNumber,
                'customer_id' => $data['customer_id'] ?? null,
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'sale_date' => $saleDate,
                'created_by_user_id' => $user->id,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create Sale Lines & handle Stock Movements (with Breakage if applicable)
            foreach ($linesToCreate as $lineData) {
                $saleLine = $sale->lines()->create($lineData);

                $targetUnit = StockUnit::lockForUpdate()->find($lineData['stock_unit_id']);
                $quantity = (float) $lineData['quantity'];

                if (! empty($lineData['source_stock_unit_id'])) {
                    // CASSURE (Breakage): Open source container (e.g., carton of 12)
                    $sourceUnit = StockUnit::lockForUpdate()->find($lineData['source_stock_unit_id']);
                    $sourceUnit->decrement('current_stock', 1);

                    // Log BREAKAGE_OUT on source unit
                    $this->stockMovementService->recordMovement([
                        'product_id' => $lineData['product_id'],
                        'stock_unit_id' => $sourceUnit->id,
                        'type' => MovementType::BREAKAGE_OUT,
                        'quantity' => 1,
                        'direction' => 'out',
                        'reference_type' => SaleLine::class,
                        'reference_id' => $saleLine->id,
                        'created_by_user_id' => $user->id,
                        'movement_date' => $saleDate,
                        'notes' => "Ouverture de {$sourceUnit->name} pour vente en {$targetUnit->name}",
                    ]);

                    // Add full equivalence to target (base) unit
                    $equivalence = (float) $sourceUnit->base_unit_equivalent;
                    $targetUnit->increment('current_stock', $equivalence);

                    // Log BREAKAGE_IN on target unit
                    $this->stockMovementService->recordMovement([
                        'product_id' => $lineData['product_id'],
                        'stock_unit_id' => $targetUnit->id,
                        'type' => MovementType::BREAKAGE_IN,
                        'quantity' => $equivalence,
                        'direction' => 'in',
                        'reference_type' => SaleLine::class,
                        'reference_id' => $saleLine->id,
                        'created_by_user_id' => $user->id,
                        'movement_date' => $saleDate,
                        'notes' => "Reliquat réintégré suite à ouverture de {$sourceUnit->name}",
                    ]);

                    // Deduct actual sold quantity from target (base) unit
                    $targetUnit->decrement('current_stock', $quantity);

                    // Log SALE movement on sold unit
                    $this->stockMovementService->recordMovement([
                        'product_id' => $lineData['product_id'],
                        'stock_unit_id' => $targetUnit->id,
                        'type' => MovementType::SALE,
                        'quantity' => $quantity,
                        'direction' => 'out',
                        'reference_type' => SaleLine::class,
                        'reference_id' => $saleLine->id,
                        'created_by_user_id' => $user->id,
                        'movement_date' => $saleDate,
                        'notes' => "Vente N° {$sale->sale_number} (avec cassure)",
                    ]);
                } else {
                    // Direct sale without breakage
                    $targetUnit->decrement('current_stock', $quantity);

                    $this->stockMovementService->recordMovement([
                        'product_id' => $lineData['product_id'],
                        'stock_unit_id' => $targetUnit->id,
                        'type' => MovementType::SALE,
                        'quantity' => $quantity,
                        'direction' => 'out',
                        'reference_type' => SaleLine::class,
                        'reference_id' => $saleLine->id,
                        'created_by_user_id' => $user->id,
                        'movement_date' => $saleDate,
                        'notes' => "Vente N° {$sale->sale_number}",
                    ]);
                }
            }

            // Create Immutable Invoice automatically
            $this->createInvoiceForSale($sale, $user);

            return $sale;
        });
    }

    protected function createInvoiceForSale(Sale $sale, User $user): Invoice
    {
        $invoiceNumber = $this->generateInvoiceNumber();

        $status = match (true) {
            $sale->remaining_amount <= 0 => InvoiceStatus::PAID,
            $sale->paid_amount > 0 => InvoiceStatus::PARTIALLY_PAID,
            default => InvoiceStatus::UNPAID,
        };

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'invoice_date' => $sale->sale_date,
            'subtotal_amount' => $sale->total_amount,
            'discount_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => $sale->total_amount,
            'paid_amount' => $sale->paid_amount,
            'remaining_amount' => $sale->remaining_amount,
            'status' => $status,
            'payment_method' => $sale->paid_amount > 0 ? 'Espèces' : null,
            'created_by_user_id' => $user->id,
        ]);

        foreach ($sale->lines as $line) {
            $invoice->lines()->create([
                'product_id' => $line->product_id,
                'stock_unit_id' => $line->stock_unit_id,
                'quantity' => $line->quantity,
                'unit_price' => $line->unit_price,
                'discount_reason' => $line->discount_reason,
                'subtotal' => $line->subtotal,
            ]);
        }

        return $invoice;
    }

    protected function generateSaleNumber(): string
    {
        $dateStr = now()->format('Ymd');
        $lastSale = Sale::whereDate('created_at', now()->today())
            ->latest('id')
            ->first();

        $sequence = $lastSale ? ((int) substr($lastSale->sale_number, -4)) + 1 : 1;

        return sprintf('VNT-%s-%04d', $dateStr, $sequence);
    }

    protected function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $lastInvoice = Invoice::latest('id')->first();
        $sequence = $lastInvoice ? ((int) substr($lastInvoice->invoice_number, -5)) + 1 : 1;

        return sprintf('FAC-%s-%05d', $year, $sequence);
    }
}

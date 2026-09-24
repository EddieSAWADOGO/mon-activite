<?php

namespace App\Domain\Stock\Services;

use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Models\StockMovement;
use App\Support\Enums\MovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    /**
     * Record a stock movement and adjust the stock unit counter.
     */
    public function recordMovement(
        int $productId,
        int $stockUnitId,
        MovementType $type,
        float $quantity,
        string $direction, // 'in' or 'out'
        ?Model $reference = null,
        int $userId = 1,
        ?\DateTimeInterface $movementDate = null,
        ?string $notes = null
    ): StockMovement {
        return DB::transaction(function () use (
            $productId,
            $stockUnitId,
            $type,
            $quantity,
            $direction,
            $reference,
            $userId,
            $movementDate,
            $notes
        ) {
            $stockUnit = StockUnit::where('id', $stockUnitId)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($direction === 'in') {
                $stockUnit->current_stock = (float)$stockUnit->current_stock + $quantity;
            } elseif ($direction === 'out') {
                $stockUnit->current_stock = (float)$stockUnit->current_stock - $quantity;
            }

            $stockUnit->save();

            return StockMovement::create([
                'product_id' => $productId,
                'stock_unit_id' => $stockUnitId,
                'type' => $type,
                'quantity' => $quantity,
                'direction' => $direction,
                'reference_type' => $reference ? $reference->getMorphClass() : null,
                'reference_id' => $reference ? $reference->getKey() : null,
                'created_by_user_id' => $userId,
                'movement_date' => $movementDate ?? now(),
                'notes' => $notes,
            ]);
        });
    }
}

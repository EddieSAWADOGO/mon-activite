<?php

namespace App\Domain\Stock\Services;

use App\Domain\Produits\Models\StockUnit;
use App\Domain\Stock\Models\StockMovement;
use App\Models\User;
use App\Support\Enums\MovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    /**
     * Record a stock movement and adjust the stock unit counter.
     * Supports both array and positional/named arguments.
     */
    public function recordMovement(
        int|array $productId,
        int|User|null $stockUnitId = null,
        ?MovementType $type = null,
        ?float $quantity = null,
        ?string $direction = null,
        ?Model $reference = null,
        int $userId = 1,
        ?\DateTimeInterface $movementDate = null,
        ?string $notes = null
    ): StockMovement {
        if (is_array($productId)) {
            $data = $productId;
            $userParam = $stockUnitId instanceof User ? $stockUnitId : null;

            $productId = (int) $data['product_id'];
            $stockUnitId = (int) $data['stock_unit_id'];
            $type = $data['type'];
            $quantity = (float) $data['quantity'];
            $direction = $data['direction'];

            if (isset($data['reference']) && $data['reference'] instanceof Model) {
                $reference = $data['reference'];
            } elseif (isset($data['reference_type']) && isset($data['reference_id'])) {
                $refClass = $data['reference_type'];
                $refId = $data['reference_id'];
                $reference = $refClass::find($refId);
            } else {
                $reference = null;
            }

            $userId = (int) ($data['created_by_user_id'] ?? ($userParam ? $userParam->id : 1));

            if (isset($data['movement_date'])) {
                $mDate = $data['movement_date'];
                $movementDate = is_string($mDate) ? new \DateTime($mDate) : $mDate;
            } else {
                $movementDate = null;
            }

            $notes = $data['notes'] ?? null;
        }

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

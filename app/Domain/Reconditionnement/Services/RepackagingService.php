<?php

namespace App\Domain\Reconditionnement\Services;

use App\Domain\Produits\Models\StockUnit;
use App\Domain\Reconditionnement\Models\Repackaging;
use App\Domain\Stock\Services\StockMovementService;
use App\Models\User;
use App\Support\Enums\MovementType;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class RepackagingService
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    public function executeRepackaging(array $data, User $user): Repackaging
    {
        return DB::transaction(function () use ($data, $user) {
            if ($data['source_stock_unit_id'] == $data['target_stock_unit_id']) {
                throw new InvalidArgumentException("L'unité de départ et l'unité d'arrivée doivent être différentes.");
            }

            /** @var StockUnit $sourceUnit */
            $sourceUnit = StockUnit::where('id', $data['source_stock_unit_id'])->lockForUpdate()->firstOrFail();

            /** @var StockUnit $targetUnit */
            $targetUnit = StockUnit::where('id', $data['target_stock_unit_id'])->lockForUpdate()->firstOrFail();

            if ($sourceUnit->product_id != $data['product_id'] || $targetUnit->product_id != $data['product_id']) {
                throw new InvalidArgumentException("Les unités sélectionnées doivent appartenir au produit concerné.");
            }

            $sourceQty = (float) $data['source_quantity'];
            $targetQty = (float) $data['target_quantity'];

            if ($sourceQty <= 0 || $targetQty <= 0) {
                throw new InvalidArgumentException("Les quantités doivent être supérieures à zéro.");
            }

            if ((float) $sourceUnit->current_stock < $sourceQty) {
                throw new InvalidArgumentException("Stock insuffisant sur l'unité de départ ({$sourceUnit->name}). Stock disponible : {$sourceUnit->current_stock}.");
            }

            $totalBaseSource = $sourceQty * (float) $sourceUnit->base_unit_equivalent;
            $totalBaseTarget = $targetQty * (float) $targetUnit->base_unit_equivalent;

            if (abs($totalBaseSource - $totalBaseTarget) > 0.0001) {
                throw new InvalidArgumentException("Incohérence d'équivalence : le volume prélevé ({$totalBaseSource} unités de base) ne correspond pas au volume obtenu ({$totalBaseTarget} unités de base).");
            }

            $dateStr = now()->format('Ymd');
            $random = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $repackagingNumber = "REC-{$dateStr}-{$random}";

            $repackaging = Repackaging::create([
                'repackaging_number' => $repackagingNumber,
                'product_id' => $data['product_id'],
                'source_stock_unit_id' => $sourceUnit->id,
                'source_quantity' => $sourceQty,
                'target_stock_unit_id' => $targetUnit->id,
                'target_quantity' => $targetQty,
                'repackaging_date' => $data['repackaging_date'] ?? now(),
                'notes' => $data['notes'] ?? null,
                'created_by_user_id' => $user->id,
            ]);

            // Sortie sur l'unité source
            $this->stockMovementService->recordMovement([
                'product_id' => $repackaging->product_id,
                'stock_unit_id' => $sourceUnit->id,
                'type' => MovementType::REPACKAGING_OUT,
                'quantity' => $sourceQty,
                'direction' => 'out',
                'reference_type' => Repackaging::class,
                'reference_id' => $repackaging->id,
                'movement_date' => $repackaging->repackaging_date,
                'notes' => "Reconditionnement (Sortie) N° {$repackaging->repackaging_number}",
            ], $user);

            // Entrée sur l'unité cible
            $this->stockMovementService->recordMovement([
                'product_id' => $repackaging->product_id,
                'stock_unit_id' => $targetUnit->id,
                'type' => MovementType::REPACKAGING_IN,
                'quantity' => $targetQty,
                'direction' => 'in',
                'reference_type' => Repackaging::class,
                'reference_id' => $repackaging->id,
                'movement_date' => $repackaging->repackaging_date,
                'notes' => "Reconditionnement (Entrée) N° {$repackaging->repackaging_number}",
            ], $user);

            return $repackaging;
        });
    }
}

<?php

namespace App\Domain\Pertes\Services;

use App\Domain\Pertes\Models\Loss;
use App\Domain\Stock\Services\StockMovementService;
use App\Models\User;
use App\Support\Enums\MovementType;
use Illuminate\Support\Facades\DB;

class LossService
{
    public function __construct(
        protected StockMovementService $stockMovementService
    ) {}

    public function recordLoss(array $data, User $user): Loss
    {
        return DB::transaction(function () use ($data, $user) {
            $dateStr = now()->format('Ymd');
            $random = str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
            $lossNumber = "PRT-{$dateStr}-{$random}";

            $loss = Loss::create([
                'loss_number' => $lossNumber,
                'product_id' => $data['product_id'],
                'stock_unit_id' => $data['stock_unit_id'],
                'quantity' => $data['quantity'],
                'reason' => $data['reason'],
                'loss_date' => $data['loss_date'] ?? now(),
                'notes' => $data['notes'] ?? null,
                'customer_return_id' => $data['customer_return_id'] ?? null,
                'created_by_user_id' => $user->id,
            ]);

            // Only decrement stock if the loss is from warehouse inventory (i.e. not a discarded customer return that was never in stock)
            if (empty($data['customer_return_id'])) {
                $this->stockMovementService->recordMovement([
                    'product_id' => $loss->product_id,
                    'stock_unit_id' => $loss->stock_unit_id,
                    'type' => MovementType::LOSS,
                    'quantity' => $loss->quantity,
                    'direction' => 'out',
                    'reference_type' => Loss::class,
                    'reference_id' => $loss->id,
                    'movement_date' => $loss->loss_date,
                    'notes' => "Sortie pour perte ({$loss->reason}) - N° {$loss->loss_number}",
                ], $user);
            }

            return $loss;
        });
    }
}

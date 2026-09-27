<?php

namespace App\Domain\Retours\Services;

use App\Domain\Pertes\Services\LossService;
use App\Domain\Retours\Models\CustomerReturn;
use App\Domain\Stock\Services\StockMovementService;
use App\Models\User;
use App\Support\Enums\MovementType;
use DomainException;
use Illuminate\Support\Facades\DB;

class CustomerReturnService
{
    public function __construct(
        protected StockMovementService $stockMovementService,
        protected LossService $lossService
    ) {}

    public function recordReturn(array $data, User $user): CustomerReturn
    {
        $dateStr = now()->format('Ymd');
        $count = CustomerReturn::whereDate('created_at', now()->today())->count() + 1;
        $returnNumber = sprintf('RET-%s-%04d', $dateStr, $count);
        while (CustomerReturn::where('return_number', $returnNumber)->exists()) {
            $count++;
            $returnNumber = sprintf('RET-%s-%04d', $dateStr, $count);
        }

        return CustomerReturn::create([
            'return_number' => $returnNumber,
            'customer_id' => $data['customer_id'] ?? null,
            'invoice_id' => $data['invoice_id'] ?? null,
            'product_id' => $data['product_id'],
            'stock_unit_id' => $data['stock_unit_id'],
            'quantity' => $data['quantity'],
            'reason' => $data['reason'],
            'status' => 'pending',
            'return_date' => $data['return_date'] ?? now(),
            'created_by_user_id' => $user->id,
            'notes' => $data['notes'] ?? null,
        ]);
    }

    public function validateAndRestock(CustomerReturn $return, User $validator): CustomerReturn
    {
        return DB::transaction(function () use ($return, $validator) {
            /** @var CustomerReturn $lockedReturn */
            $lockedReturn = CustomerReturn::where('id', $return->id)->lockForUpdate()->firstOrFail();

            if (! $lockedReturn->isPending()) {
                throw new DomainException("Ce retour client a déjà été traité.");
            }

            $lockedReturn->update([
                'status' => 'restocked',
                'validated_by_user_id' => $validator->id,
                'validated_at' => now(),
            ]);

            $this->stockMovementService->recordMovement([
                'product_id' => $lockedReturn->product_id,
                'stock_unit_id' => $lockedReturn->stock_unit_id,
                'type' => MovementType::RETURN,
                'quantity' => $lockedReturn->quantity,
                'direction' => 'in',
                'reference_type' => CustomerReturn::class,
                'reference_id' => $lockedReturn->id,
                'movement_date' => now(),
                'notes' => "Réintégration stock suite au retour client N° {$lockedReturn->return_number}",
            ], $validator);

            return $lockedReturn;
        });
    }

    public function validateAndDiscard(CustomerReturn $return, User $validator, ?string $notes = null): CustomerReturn
    {
        return DB::transaction(function () use ($return, $validator, $notes) {
            /** @var CustomerReturn $lockedReturn */
            $lockedReturn = CustomerReturn::where('id', $return->id)->lockForUpdate()->firstOrFail();

            if (! $lockedReturn->isPending()) {
                throw new DomainException("Ce retour client a déjà été traité.");
            }

            $lockedReturn->update([
                'status' => 'discarded',
                'validated_by_user_id' => $validator->id,
                'validated_at' => now(),
            ]);

            $this->lossService->recordLoss([
                'product_id' => $lockedReturn->product_id,
                'stock_unit_id' => $lockedReturn->stock_unit_id,
                'quantity' => $lockedReturn->quantity,
                'reason' => "Produit non récupérable (Retour client N° {$lockedReturn->return_number})",
                'loss_date' => now(),
                'notes' => $notes ?? "Déclaré en perte suite au retour N° {$lockedReturn->return_number}",
                'customer_return_id' => $lockedReturn->id,
            ], $validator);

            return $lockedReturn;
        });
    }
}

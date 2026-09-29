<?php

namespace App\Domain\Achats\Http\Requests;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Produits\Models\StockUnit;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Purchase::class);
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'exists:products,id'],
            'lines.*.stock_unit_id' => ['required', 'exists:stock_units,id'],
            'lines.*.quantity' => ['required', 'numeric', 'gt:0'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $lines = $this->input('lines', []);
            $totalPurchaseAmount = 0;

            foreach ($lines as $index => $line) {
                if (! empty($line['product_id']) && ! empty($line['stock_unit_id'])) {
                    $stockUnit = StockUnit::find($line['stock_unit_id']);
                    if (! $stockUnit || $stockUnit->product_id != $line['product_id']) {
                        $validator->errors()->add(
                            "lines.{$index}.stock_unit_id",
                            "L'unité sélectionnée sur la ligne #" . ($index + 1) . " doit appartenir au produit sélectionné."
                        );
                    }
                }
                $q = (float) ($line['quantity'] ?? 0);
                $p = (float) ($line['unit_price'] ?? 0);
                $totalPurchaseAmount += ($q * $p);
            }

            $paidAmount = (float) $this->input('paid_amount', 0);
            if ($paidAmount > $totalPurchaseAmount) {
                $validator->errors()->add(
                    'paid_amount',
                    "Le montant payé immédiatement (" . number_format($paidAmount, 0, ',', ' ') . " FCFA) ne peut pas dépasser le montant total de l'achat (" . number_format($totalPurchaseAmount, 0, ',', ' ') . " FCFA)."
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'supplier_id' => 'fournisseur',
            'purchase_date' => 'date d\'achat',
            'paid_amount' => 'montant payé',
            'notes' => 'notes',
            'lines' => 'lignes d\'achat',
            'lines.*.product_id' => 'produit',
            'lines.*.stock_unit_id' => 'unité',
            'lines.*.quantity' => 'quantité',
            'lines.*.unit_price' => 'prix unitaire',
        ];
    }
}

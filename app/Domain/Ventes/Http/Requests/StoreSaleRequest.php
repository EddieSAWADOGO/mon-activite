<?php

namespace App\Domain\Ventes\Http\Requests;

use App\Domain\Produits\Models\StockUnit;
use App\Domain\Ventes\Models\Sale;
use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Sale::class);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'sale_date' => ['required', 'date'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'exists:products,id'],
            'lines.*.stock_unit_id' => ['required', 'exists:stock_units,id'],
            'lines.*.quantity' => ['required', 'numeric', 'gt:0'],
            'lines.*.unit_price' => ['required', 'numeric', 'min:0'],
            'lines.*.discount_reason' => ['nullable', 'string', 'max:255'],
            'lines.*.source_stock_unit_id' => ['nullable', 'exists:stock_units,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $lines = $this->input('lines', []);

            foreach ($lines as $index => $line) {
                if (empty($line['stock_unit_id'])) {
                    continue;
                }

                $stockUnit = StockUnit::find($line['stock_unit_id']);
                if (! $stockUnit) {
                    continue;
                }

                // Rule: Price deviation vs default_selling_price requires discount_reason
                $unitPrice = (float) ($line['unit_price'] ?? 0);
                $defaultPrice = (float) $stockUnit->default_selling_price;

                if (abs($unitPrice - $defaultPrice) > 0.01 && empty(trim($line['discount_reason'] ?? ''))) {
                    $validator->errors()->add(
                        "lines.{$index}.discount_reason",
                        "Le motif de l'écart de prix est obligatoire lorsque le prix saisi (" . number_format($unitPrice, 0, ',', ' ') . " FCFA) diffère du prix par défaut (" . number_format($defaultPrice, 0, ',', ' ') . " FCFA)."
                    );
                }

                // Rule: If source_stock_unit_id is specified (cassure), ensure source unit belongs to the same product and has stock
                if (! empty($line['source_stock_unit_id'])) {
                    $sourceUnit = StockUnit::find($line['source_stock_unit_id']);
                    if (! $sourceUnit || $sourceUnit->product_id != $line['product_id']) {
                        $validator->errors()->add(
                            "lines.{$index}.source_stock_unit_id",
                            "L'unité source pour la cassure doit appartenir au même produit."
                        );
                    } elseif ($sourceUnit->current_stock < 1) {
                        $validator->errors()->add(
                            "lines.{$index}.source_stock_unit_id",
                            "Stock insuffisant pour l'unité source " . $sourceUnit->name . " (stock actuel: " . $sourceUnit->current_stock . ")."
                        );
                    }
                } else {
                    // Direct sale: check stock on the target unit
                    if ($stockUnit->current_stock < $line['quantity']) {
                        $validator->errors()->add(
                            "lines.{$index}.quantity",
                            "Stock insuffisant pour " . $stockUnit->name . " (disponible: " . $stockUnit->current_stock . ", demandé: " . $line['quantity'] . "). Indiquez une unité source s'il faut ouvrir un carton."
                        );
                    }
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'Client',
            'sale_date' => 'Date de vente',
            'paid_amount' => 'Montant payé',
            'lines' => 'Lignes de vente',
        ];
    }
}

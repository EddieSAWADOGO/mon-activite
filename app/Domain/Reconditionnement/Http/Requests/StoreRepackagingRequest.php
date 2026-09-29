<?php

namespace App\Domain\Reconditionnement\Http\Requests;

use App\Domain\Produits\Models\StockUnit;
use App\Domain\Reconditionnement\Models\Repackaging;
use Illuminate\Foundation\Http\FormRequest;

class StoreRepackagingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Repackaging::class);
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'source_stock_unit_id' => ['required', 'integer', 'exists:stock_units,id', 'different:target_stock_unit_id'],
            'source_quantity' => ['required', 'numeric', 'gt:0'],
            'target_stock_unit_id' => ['required', 'integer', 'exists:stock_units,id'],
            'target_quantity' => ['required', 'numeric', 'gt:0'],
            'repackaging_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $productId = $this->input('product_id');
            $sourceUnitId = $this->input('source_stock_unit_id');
            $targetUnitId = $this->input('target_stock_unit_id');

            if ($productId && $sourceUnitId) {
                $sourceUnit = StockUnit::find($sourceUnitId);
                if (! $sourceUnit || $sourceUnit->product_id != $productId) {
                    $validator->errors()->add('source_stock_unit_id', "L'unité de départ doit appartenir au produit sélectionné.");
                }
            }

            if ($productId && $targetUnitId) {
                $targetUnit = StockUnit::find($targetUnitId);
                if (! $targetUnit || $targetUnit->product_id != $productId) {
                    $validator->errors()->add('target_stock_unit_id', "L'unité d'arrivée doit appartenir au produit sélectionné.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Le produit est obligatoire.',
            'source_stock_unit_id.required' => "L'unité de départ est obligatoire.",
            'source_stock_unit_id.different' => "L'unité de départ et l'unité d'arrivée doivent être différentes.",
            'source_quantity.required' => 'La quantité prélevée est obligatoire.',
            'source_quantity.gt' => 'La quantité doit être supérieure à zéro.',
            'target_stock_unit_id.required' => "L'unité d'arrivée est obligatoire.",
            'target_quantity.required' => 'La quantité obtenue est obligatoire.',
            'target_quantity.gt' => 'La quantité doit être supérieure à zéro.',
            'repackaging_date.required' => 'La date est obligatoire.',
        ];
    }
}

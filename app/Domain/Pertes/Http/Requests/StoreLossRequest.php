<?php

namespace App\Domain\Pertes\Http\Requests;

use App\Domain\Pertes\Models\Loss;
use App\Domain\Produits\Models\StockUnit;
use Illuminate\Foundation\Http\FormRequest;

class StoreLossRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Loss::class);
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'stock_unit_id' => ['required', 'integer', 'exists:stock_units,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string', 'max:255'],
            'loss_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('product_id') && $this->filled('stock_unit_id')) {
                $stockUnit = StockUnit::find($this->input('stock_unit_id'));
                if (! $stockUnit || $stockUnit->product_id != $this->input('product_id')) {
                    $validator->errors()->add('stock_unit_id', "L'unité doit appartenir au produit sélectionné.");
                } elseif ($this->filled('quantity')) {
                    $qty = (float) $this->input('quantity');
                    if ($qty > (float) $stockUnit->current_stock) {
                        $validator->errors()->add(
                            'quantity',
                            "La quantité perdue ({$qty}) ne peut pas dépasser le stock actuel disponible (" . $stockUnit->current_stock . ")."
                        );
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Le produit concerné est obligatoire.',
            'stock_unit_id.required' => "L'unité est obligatoire.",
            'quantity.required' => 'La quantité perdue est obligatoire.',
            'quantity.gt' => 'La quantité doit être supérieure à zéro.',
            'reason.required' => 'Le motif de la perte est obligatoire.',
            'loss_date.required' => 'La date de la constatation est obligatoire.',
        ];
    }
}

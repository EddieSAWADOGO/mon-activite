<?php

namespace App\Domain\Stock\Http\Requests;

use App\Domain\Produits\Models\StockUnit;
use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->canManageInventoryOperations();
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'stock_unit_id' => ['required', 'integer', 'exists:stock_units,id'],
            'physical_quantity' => ['required', 'numeric', 'min:0'],
            'inventory_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('product_id') && $this->filled('stock_unit_id')) {
                $stockUnit = StockUnit::find($this->input('stock_unit_id'));
                if (! $stockUnit || $stockUnit->product_id != $this->input('product_id')) {
                    $validator->errors()->add('stock_unit_id', "L'unité sélectionnée doit appartenir au produit sélectionné.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Le produit concerné est obligatoire.',
            'stock_unit_id.required' => "L'unité de comptage est obligatoire.",
            'physical_quantity.required' => 'La quantité physique comptée est obligatoire.',
            'physical_quantity.min' => 'La quantité physique ne peut pas être négative.',
            'inventory_date.required' => "La date de réalisation de l'inventaire est obligatoire.",
        ];
    }
}

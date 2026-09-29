<?php

namespace App\Domain\Retours\Http\Requests;

use App\Domain\Produits\Models\StockUnit;
use App\Domain\Retours\Models\CustomerReturn;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', CustomerReturn::class);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'invoice_id' => ['nullable', 'integer', 'exists:invoices,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'stock_unit_id' => ['required', 'integer', 'exists:stock_units,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string', 'max:255'],
            'return_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('product_id') && $this->filled('stock_unit_id')) {
                $stockUnit = StockUnit::find($this->input('stock_unit_id'));
                if (! $stockUnit || $stockUnit->product_id != $this->input('product_id')) {
                    $validator->errors()->add('stock_unit_id', "L'unité sélectionnée doit appartenir au produit retourné.");
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Le produit est obligatoire.',
            'stock_unit_id.required' => "L'unité est obligatoire.",
            'quantity.required' => 'La quantité retournée est obligatoire.',
            'quantity.gt' => 'La quantité doit être supérieure à zéro.',
            'reason.required' => 'Le motif du retour est obligatoire.',
            'return_date.required' => 'La date du retour est obligatoire.',
        ];
    }
}

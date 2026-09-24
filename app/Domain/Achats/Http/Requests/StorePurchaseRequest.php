<?php

namespace App\Domain\Achats\Http\Requests;

use App\Domain\Achats\Models\Purchase;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Purchase::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'paid_amount' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.product_id' => ['required', 'exists:products,id'],
            'lines.*.stock_unit_id' => ['required', 'exists:stock_units,id'],
            'lines.*.quantity' => ['required', 'numeric', 'gt:0'],
            'lines.*.unit_price' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
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

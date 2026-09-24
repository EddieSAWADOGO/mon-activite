<?php

namespace App\Domain\Pertes\Http\Requests;

use App\Domain\Pertes\Models\Loss;
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

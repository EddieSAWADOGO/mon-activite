<?php

namespace App\Domain\Paiements\Http\Requests;

use App\Domain\Achats\Models\Purchase;
use App\Domain\Paiements\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchasePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Payment::class)
            && $this->user()->can('create', Purchase::class);
    }

    public function rules(): array
    {
        return [
            'purchase_id' => ['required', 'integer', 'exists:purchases,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_id.required' => 'Le bon d\'achat concerné est obligatoire.',
            'purchase_id.exists' => 'Le bon d\'achat sélectionné est introuvable.',
            'amount.required' => 'Le montant du règlement est obligatoire.',
            'amount.gt' => 'Le montant doit être supérieur à zéro.',
            'payment_date.required' => 'La date du règlement est obligatoire.',
            'payment_method.required' => 'Le mode de paiement est obligatoire.',
        ];
    }
}

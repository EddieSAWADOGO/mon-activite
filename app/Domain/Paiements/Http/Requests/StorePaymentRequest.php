<?php

namespace App\Domain\Paiements\Http\Requests;

use App\Domain\Paiements\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Payment::class);
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
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
            'invoice_id.required' => 'La facture concernée est obligatoire.',
            'invoice_id.exists' => 'La facture sélectionnée est introuvable.',
            'amount.required' => 'Le montant du règlement est obligatoire.',
            'amount.gt' => 'Le montant doit être supérieur à zéro.',
            'payment_date.required' => 'La date du règlement est obligatoire.',
            'payment_method.required' => 'Le mode de paiement est obligatoire.',
        ];
    }
}

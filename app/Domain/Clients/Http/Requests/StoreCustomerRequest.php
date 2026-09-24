<?php

namespace App\Domain\Clients\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Domain\Clients\Models\Customer::class);
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:particulier,entreprise'],
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'ifu' => ['nullable', 'string', 'max:100'],
            'rccm' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => 'Type de client',
            'name' => 'Nom / Raison sociale',
            'contact_person' => 'Personne de contact',
            'phone' => 'Téléphone',
            'whatsapp' => 'WhatsApp',
            'email' => 'Email',
            'address' => 'Adresse',
            'ifu' => 'N° IFU',
            'rccm' => 'N° RCCM',
            'notes' => 'Notes',
        ];
    }
}

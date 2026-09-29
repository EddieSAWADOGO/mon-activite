<?php

namespace App\Domain\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanySettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'ifu' => ['nullable', 'string', 'max:100'],
            'rccm' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'bank_details' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nom de l\'entreprise',
            'tagline' => 'Domaine d\'activité / Sigle',
            'ifu' => 'N° IFU',
            'rccm' => 'N° RCCM',
            'address' => 'Adresse physique',
            'phone' => 'Téléphone(s)',
            'email' => 'Adresse E-mail',
            'bank_details' => 'Coordonnées bancaires',
            'logo' => 'Logo de l\'entreprise',
        ];
    }
}

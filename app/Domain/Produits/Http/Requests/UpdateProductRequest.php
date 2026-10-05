<?php

namespace App\Domain\Produits\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $product && ($this->user()?->can('update', $product) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['required', 'boolean'],

            'existing_units' => ['nullable', 'array'],
            'existing_units.*.id' => ['required_with:existing_units', 'exists:stock_units,id'],
            'existing_units.*.name' => ['required_with:existing_units', 'string', 'max:100'],
            'existing_units.*.default_selling_price' => ['required_with:existing_units', 'numeric', 'min:0'],
            'existing_units.*.low_stock_threshold' => ['required_with:existing_units', 'numeric', 'min:0'],

            'new_units' => ['nullable', 'array'],
            'new_units.*.name' => ['required_with:new_units', 'string', 'max:100'],
            'new_units.*.base_unit_equivalent' => ['required_with:new_units', 'numeric', 'gt:0'],
            'new_units.*.default_selling_price' => ['required_with:new_units', 'numeric', 'min:0'],
            'new_units.*.low_stock_threshold' => ['required_with:new_units', 'numeric', 'min:0'],
            'new_units.*.initial_stock' => ['nullable', 'numeric', 'min:0'],
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
            'name' => 'nom du produit',
            'description' => 'description',
            'is_active' => 'statut actif',
            'existing_units.*.name' => "nom de l'unité",
            'existing_units.*.default_selling_price' => "prix de vente par défaut",
            'existing_units.*.low_stock_threshold' => "stock minimum d'alerte",
            'new_units.*.name' => "nom de la nouvelle unité",
            'new_units.*.base_unit_equivalent' => "équivalence en unité de base",
            'new_units.*.default_selling_price' => "prix de vente par défaut",
            'new_units.*.low_stock_threshold' => "stock minimum d'alerte",
        ];
    }
}

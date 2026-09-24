<?php

namespace App\Domain\Produits\Http\Requests;

use App\Domain\Produits\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
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

            'base_unit_name' => ['required', 'string', 'max:100'],
            'base_unit_price' => ['required', 'numeric', 'min:0'],
            'base_unit_low_stock_threshold' => ['required', 'numeric', 'min:0'],
            'base_unit_initial_stock' => ['nullable', 'numeric', 'min:0'],

            'additional_units' => ['nullable', 'array'],
            'additional_units.*.name' => ['required_with:additional_units', 'string', 'max:100'],
            'additional_units.*.base_unit_equivalent' => ['required_with:additional_units', 'numeric', 'gt:0'],
            'additional_units.*.default_selling_price' => ['required_with:additional_units', 'numeric', 'min:0'],
            'additional_units.*.low_stock_threshold' => ['required_with:additional_units', 'numeric', 'min:0'],
            'additional_units.*.initial_stock' => ['nullable', 'numeric', 'min:0'],
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
            'base_unit_name' => "nom de l'unité de base",
            'base_unit_price' => "prix de vente de l'unité de base",
            'base_unit_low_stock_threshold' => "seuil d'alerte de l'unité de base",
            'base_unit_initial_stock' => "stock initial de l'unité de base",
            'additional_units.*.name' => "nom de l'unité additionnelle",
            'additional_units.*.base_unit_equivalent' => "équivalence en unité de base",
            'additional_units.*.default_selling_price' => "prix de vente par défaut",
            'additional_units.*.low_stock_threshold' => "seuil d'alerte",
            'additional_units.*.initial_stock' => "stock initial",
        ];
    }
}

<?php

namespace App\Domain\Produits\Services;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProductService
{
    /**
     * Create a new product with its base unit and optional additional declared units.
     *
     * @param  array<string, mixed>  $data
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active' => true,
            ]);

            // Create base unit
            StockUnit::create([
                'product_id' => $product->id,
                'name' => $data['base_unit_name'],
                'is_base_unit' => true,
                'base_unit_equivalent' => 1.0000,
                'default_selling_price' => (int) round($data['base_unit_price']),
                'low_stock_threshold' => $data['base_unit_low_stock_threshold'],
                'current_stock' => $data['base_unit_initial_stock'] ?? 0,
                'is_active' => true,
            ]);

            // Create additional declared units
            if (! empty($data['additional_units']) && is_array($data['additional_units'])) {
                foreach ($data['additional_units'] as $unitData) {
                    if (blank($unitData['name'] ?? null)) {
                        continue;
                    }

                    StockUnit::create([
                        'product_id' => $product->id,
                        'name' => $unitData['name'],
                        'is_base_unit' => false,
                        'base_unit_equivalent' => $unitData['base_unit_equivalent'],
                        'default_selling_price' => (int) round($unitData['default_selling_price']),
                        'low_stock_threshold' => $unitData['low_stock_threshold'],
                        'current_stock' => $unitData['initial_stock'] ?? 0,
                        'is_active' => true,
                    ]);
                }
            }

            return $product->load('units');
        });
    }

    /**
     * Update an existing product, update mutable unit fields, and append new units.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active' => (bool) $data['is_active'],
            ]);

            // Update existing units mutable fields (never change equivalence to respect immutability)
            if (! empty($data['existing_units']) && is_array($data['existing_units'])) {
                foreach ($data['existing_units'] as $unitData) {
                    $unit = StockUnit::where('product_id', $product->id)
                        ->where('id', $unitData['id'])
                        ->first();

                    if ($unit) {
                        $unit->update([
                            'name' => $unitData['name'],
                            'default_selling_price' => (int) round($unitData['default_selling_price']),
                            'low_stock_threshold' => $unitData['low_stock_threshold'],
                        ]);
                    }
                }
            }

            // Create new declared units
            if (! empty($data['new_units']) && is_array($data['new_units'])) {
                foreach ($data['new_units'] as $unitData) {
                    if (blank($unitData['name'] ?? null)) {
                        continue;
                    }

                    StockUnit::create([
                        'product_id' => $product->id,
                        'name' => $unitData['name'],
                        'is_base_unit' => false,
                        'base_unit_equivalent' => $unitData['base_unit_equivalent'],
                        'default_selling_price' => (int) round($unitData['default_selling_price']),
                        'low_stock_threshold' => $unitData['low_stock_threshold'],
                        'current_stock' => $unitData['initial_stock'] ?? 0,
                        'is_active' => true,
                    ]);
                }
            }

            return $product->fresh('units');
        });
    }

    /**
     * Toggle active/archived status of a stock unit.
     */
    public function toggleUnitStatus(Product $product, StockUnit $stockUnit): StockUnit
    {
        if ($stockUnit->product_id !== $product->id) {
            throw new InvalidArgumentException("Cette unité n'appartient pas à ce produit.");
        }

        if ($stockUnit->is_base_unit) {
            throw new InvalidArgumentException("L'unité de base ne peut pas être désactivée.");
        }

        $stockUnit->update([
            'is_active' => ! $stockUnit->is_active,
        ]);

        return $stockUnit;
    }

    /**
     * Delete a product if allowed by business rules.
     */
    public function deleteProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            foreach ($product->units as $unit) {
                if (! $unit->canBeDeleted()) {
                    throw new InvalidArgumentException("Le produit '{$product->name}' ne peut pas être supprimé car l'unité '{$unit->name}' possède du stock ou a été utilisée dans des transactions.");
                }
            }

            $product->delete();
        });
    }
}

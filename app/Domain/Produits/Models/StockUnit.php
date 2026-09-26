<?php

namespace App\Domain\Produits\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'is_base_unit',
        'base_unit_equivalent',
        'default_selling_price',
        'low_stock_threshold',
        'current_stock',
        'is_active',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\StockUnitFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_base_unit' => 'boolean',
            'is_active' => 'boolean',
            'base_unit_equivalent' => 'decimal:4',
            'default_selling_price' => 'integer',
            'low_stock_threshold' => 'decimal:2',
            'current_stock' => 'decimal:2',
        ];
    }

    /**
     * Get the product that owns this stock unit.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Determine if this unit is currently at or below its low stock threshold.
     */
    public function isLowStock(): bool
    {
        return $this->is_active && ($this->current_stock <= $this->low_stock_threshold);
    }

    /**
     * Check if this unit can be permanently deleted.
     * Rule: Base units or units used in transactions or with stock cannot be deleted.
     */
    public function canBeDeleted(): bool
    {
        if ($this->is_base_unit) {
            return false;
        }

        if ((float) $this->current_stock > 0) {
            return false;
        }

        return ! $this->isUsedInTransactions();
    }

    /**
     * Determine if unit has been used in any transactions (purchases, sales, returns, losses, repackaging).
     */
    public function isUsedInTransactions(): bool
    {
        // Future transaction models (PurchaseLine, SaleLine, StockMovement, etc.) will be checked here.
        return false;
    }
}

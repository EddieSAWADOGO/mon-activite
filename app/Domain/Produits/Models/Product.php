<?php

namespace App\Domain\Produits\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\ProductFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get all stock units defined for the product.
     */
    public function units(): HasMany
    {
        return $this->hasMany(StockUnit::class);
    }

    /**
     * Get active stock units defined for the product.
     */
    public function activeUnits(): HasMany
    {
        return $this->hasMany(StockUnit::class)->where('is_active', true);
    }

    /**
     * Get the base unit for the product.
     */
    public function baseUnit(): HasOne
    {
        return $this->hasOne(StockUnit::class)->where('is_base_unit', true);
    }

    /**
     * Scope to filter active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search products by name or description.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope to filter products with at least one active unit below or equal to its low stock threshold.
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereHas('activeUnits', function (Builder $q) {
            $q->whereColumn('current_stock', '<=', 'low_stock_threshold');
        });
    }

    /**
     * Determine if any active unit of the product is in low stock state.
     */
    public function isLowStock(): bool
    {
        return $this->activeUnits->contains(fn (StockUnit $unit) => $unit->isLowStock());
    }

    /**
     * Check if product has additional declared units beyond the base unit.
     */
    public function hasMultipleUnits(): bool
    {
        return $this->activeUnits->count() > 1;
    }
}

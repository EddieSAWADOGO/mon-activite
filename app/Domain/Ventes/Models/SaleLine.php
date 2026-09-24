<?php

namespace App\Domain\Ventes\Models;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'stock_unit_id',
        'quantity',
        'unit_price',
        'default_unit_price',
        'discount_reason',
        'subtotal',
        'source_stock_unit_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'default_unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class);
    }

    public function sourceStockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class, 'source_stock_unit_id');
    }

    public function hasDiscount(): bool
    {
        return (float) $this->unit_price !== (float) $this->default_unit_price;
    }

    public function isCassure(): bool
    {
        return ! is_null($this->source_stock_unit_id);
    }
}

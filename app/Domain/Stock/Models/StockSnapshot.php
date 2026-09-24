<?php

namespace App\Domain\Stock\Models;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'product_id',
        'stock_unit_id',
        'quantity',
        'snapshot_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'snapshot_date' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class);
    }
}

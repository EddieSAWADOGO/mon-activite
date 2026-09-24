<?php

namespace App\Domain\Reconditionnement\Models;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repackaging extends Model
{
    use HasFactory;

    protected $fillable = [
        'repackaging_number',
        'product_id',
        'source_stock_unit_id',
        'source_quantity',
        'target_stock_unit_id',
        'target_quantity',
        'repackaging_date',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'source_quantity' => 'decimal:2',
            'target_quantity' => 'decimal:2',
            'repackaging_date' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function sourceStockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class, 'source_stock_unit_id');
    }

    public function targetStockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class, 'target_stock_unit_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

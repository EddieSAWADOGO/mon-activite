<?php

namespace App\Domain\Pertes\Models;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Domain\Retours\Models\CustomerReturn;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loss extends Model
{
    use HasFactory;

    protected $fillable = [
        'loss_number',
        'product_id',
        'stock_unit_id',
        'quantity',
        'reason',
        'loss_date',
        'notes',
        'customer_return_id',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'loss_date' => 'datetime',
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

    public function customerReturn(): BelongsTo
    {
        return $this->belongsTo(CustomerReturn::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}

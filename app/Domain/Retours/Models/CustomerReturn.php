<?php

namespace App\Domain\Retours\Models;

use App\Domain\Clients\Models\Customer;
use App\Domain\Facturation\Models\Invoice;
use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'return_number',
        'customer_id',
        'invoice_id',
        'product_id',
        'stock_unit_id',
        'quantity',
        'reason',
        'status',
        'return_date',
        'created_by_user_id',
        'validated_by_user_id',
        'validated_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'return_date' => 'datetime',
            'validated_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockUnit(): BelongsTo
    {
        return $this->belongsTo(StockUnit::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by_user_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRestocked(): bool
    {
        return $this->status === 'restocked';
    }

    public function isDiscarded(): bool
    {
        return $this->status === 'discarded';
    }
}

<?php

namespace App\Domain\Facturation\Models;

use App\Domain\Produits\Models\Product;
use App\Domain\Produits\Models\StockUnit;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'stock_unit_id',
        'quantity',
        'unit_price',
        'discount_reason',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (InvoiceLine $line) {
            throw new DomainException("Les lignes de facture ne peuvent plus être modifiées (Facture immuable).");
        });

        static::deleting(function (InvoiceLine $line) {
            throw new DomainException("Les lignes de facture ne peuvent pas être supprimées (Facture immuable).");
        });
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
}

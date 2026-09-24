<?php

namespace App\Domain\Facturation\Models;

use App\Domain\Clients\Models\Customer;
use App\Domain\Ventes\Models\Sale;
use App\Models\User;
use App\Support\Enums\InvoiceStatus;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'sale_id',
        'customer_id',
        'invoice_date',
        'subtotal_amount',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'status',
        'payment_method',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'datetime',
            'subtotal_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'status' => InvoiceStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (Invoice $invoice) {
            $allowedChanges = ['status', 'paid_amount', 'remaining_amount', 'payment_method', 'updated_at'];
            $dirtyKeys = array_keys($invoice->getDirty());

            foreach ($dirtyKeys as $key) {
                if (! in_array($key, $allowedChanges, true)) {
                    throw new DomainException("Les données de fond de la facture ne peuvent plus être modifiées (Facture immuable).");
                }
            }
        });

        static::deleting(function (Invoice $invoice) {
            throw new DomainException("Les factures ne peuvent pas être supprimées (Facture immuable).");
        });
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }
}

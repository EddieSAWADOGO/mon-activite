<?php

namespace App\Domain\Achats\Models;

use App\Domain\Fournisseurs\Models\Supplier;
use App\Domain\Paiements\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number',
        'supplier_id',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'purchase_date',
        'created_by_user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'integer',
            'paid_amount' => 'integer',
            'remaining_amount' => 'integer',
            'purchase_date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseLine::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}

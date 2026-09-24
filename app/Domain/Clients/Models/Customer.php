<?php

namespace App\Domain\Clients\Models;

use App\Domain\Facturation\Models\Invoice;
use App\Domain\Ventes\Models\Sale;
use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'contact_person',
        'phone',
        'whatsapp',
        'email',
        'address',
        'ifu',
        'rccm',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}

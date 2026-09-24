<?php

namespace App\Support\Enums;

enum InvoiceStatus: string
{
    case UNPAID = 'unpaid';
    case PARTIALLY_PAID = 'partially_paid';
    case PAID = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Impayée',
            self::PARTIALLY_PAID => 'Partiellement payée',
            self::PAID => 'Payée',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::UNPAID => 'red',
            self::PARTIALLY_PAID => 'amber',
            self::PAID => 'emerald',
        };
    }
}

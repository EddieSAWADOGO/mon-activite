<?php

namespace App\Support\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case CASHIER = 'cashier';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrateur',
            self::ADMIN => 'Administrateur',
            self::CASHIER => 'Caissier',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'purple',
            self::ADMIN => 'emerald',
            self::CASHIER => 'sky',
        };
    }
}

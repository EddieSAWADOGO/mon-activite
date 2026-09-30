<?php

namespace App\Support\Enums;

enum MovementType: string
{
    case PURCHASE = 'purchase';
    case SALE = 'sale';
    case BREAKAGE_OUT = 'breakage_out';
    case BREAKAGE_IN = 'breakage_in';
    case RETURN = 'return';
    case LOSS = 'loss';
    case REPACKAGING_OUT = 'repackaging_out';
    case REPACKAGING_IN = 'repackaging_in';
    case INVENTORY_ADJUSTMENT = 'inventory_adjustment';

    public function label(): string
    {
        return match ($this) {
            self::PURCHASE => 'Achat (Entrée)',
            self::SALE => 'Vente (Sortie)',
            self::BREAKAGE_OUT => 'Cassure (Sortie unité source)',
            self::BREAKAGE_IN => 'Cassure (Entrée reliquat)',
            self::RETURN => 'Retour client (Entrée)',
            self::LOSS => 'Perte (Sortie)',
            self::REPACKAGING_OUT => 'Reconditionnement (Sortie source)',
            self::REPACKAGING_IN => 'Reconditionnement (Entrée cible)',
            self::INVENTORY_ADJUSTMENT => 'Ajustement d\'inventaire',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::PURCHASE, self::BREAKAGE_IN, self::RETURN, self::REPACKAGING_IN => 'emerald',
            self::SALE, self::BREAKAGE_OUT, self::REPACKAGING_OUT => 'sky',
            self::LOSS => 'red',
            self::INVENTORY_ADJUSTMENT => 'purple',
        };
    }
}

<?php

namespace App\Domain\Ventes\Policies;

use App\Domain\Ventes\Models\Sale;
use App\Models\User;

class SalePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Sale $sale): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true; // Cashier, Admin, SuperAdmin can make sales
    }

    public function update(User $user, Sale $sale): bool
    {
        return false; // Sales details are fixed once saved
    }

    public function delete(User $user, Sale $sale): bool
    {
        return $user->isSuperAdmin();
    }
}

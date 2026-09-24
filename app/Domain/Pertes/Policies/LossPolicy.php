<?php

namespace App\Domain\Pertes\Policies;

use App\Domain\Pertes\Models\Loss;
use App\Models\User;

class LossPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageInventoryOperations();
    }

    public function view(User $user, Loss $loss): bool
    {
        return $user->canManageInventoryOperations();
    }

    public function create(User $user): bool
    {
        return $user->canManageInventoryOperations();
    }
}

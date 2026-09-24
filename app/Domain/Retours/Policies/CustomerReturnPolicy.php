<?php

namespace App\Domain\Retours\Policies;

use App\Domain\Retours\Models\CustomerReturn;
use App\Models\User;

class CustomerReturnPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageInventoryOperations();
    }

    public function view(User $user, CustomerReturn $return): bool
    {
        return $user->canManageInventoryOperations();
    }

    public function create(User $user): bool
    {
        return $user->canManageInventoryOperations();
    }

    public function validate(User $user, CustomerReturn $return): bool
    {
        return $user->canManageInventoryOperations();
    }
}

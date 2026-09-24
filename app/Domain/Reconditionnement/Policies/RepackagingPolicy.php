<?php

namespace App\Domain\Reconditionnement\Policies;

use App\Domain\Reconditionnement\Models\Repackaging;
use App\Models\User;

class RepackagingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageInventoryOperations();
    }

    public function view(User $user, Repackaging $repackaging): bool
    {
        return $user->canManageInventoryOperations();
    }

    public function create(User $user): bool
    {
        return $user->canManageInventoryOperations();
    }
}

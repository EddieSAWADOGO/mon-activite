<?php

namespace App\Domain\Historique\Policies;

use App\Models\User;

class HistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAccessHistory();
    }
}

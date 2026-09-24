<?php

namespace App\Domain\Utilisateurs\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $authenticatedUser): bool
    {
        return $authenticatedUser->canManageUsers();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $authenticatedUser, User $targetUser): bool
    {
        return $authenticatedUser->canManageUsers() || $authenticatedUser->id === $targetUser->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $authenticatedUser): bool
    {
        return $authenticatedUser->canManageUsers();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authenticatedUser, User $targetUser): bool
    {
        // Super Admin can update anyone
        if ($authenticatedUser->isSuperAdmin()) {
            return true;
        }

        // Admin cannot update Super Admin
        if ($authenticatedUser->isAdmin() && $targetUser->isSuperAdmin()) {
            return false;
        }

        return $authenticatedUser->canManageUsers();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authenticatedUser, User $targetUser): bool
    {
        // Cannot delete self
        if ($authenticatedUser->id === $targetUser->id) {
            return false;
        }

        if ($authenticatedUser->isSuperAdmin()) {
            return true;
        }

        if ($authenticatedUser->isAdmin() && !$targetUser->isSuperAdmin()) {
            return true;
        }

        return false;
    }
}

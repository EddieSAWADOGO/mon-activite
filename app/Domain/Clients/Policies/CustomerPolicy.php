<?php

namespace App\Domain\Clients\Policies;

use App\Domain\Clients\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Customer $customer): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true; // Cashier, Admin, SuperAdmin can create customers
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->canManageUsers() || $user->canManageInventoryOperations();
    }

    public function delete(User $user, Customer $customer): bool
    {
        return $user->canManageUsers();
    }
}

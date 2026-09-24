<?php

namespace App\Domain\Facturation\Policies;

use App\Domain\Facturation\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return false; // Immutable invoices
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return false; // Immutable invoices
    }
}

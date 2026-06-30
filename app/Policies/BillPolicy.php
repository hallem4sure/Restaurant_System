<?php

namespace App\Policies;

use App\Models\Bill;
use App\Models\User;

class BillPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view bills');
    }

    public function view(User $user, Bill $bill): bool
    {
        return $user->can('view bills');
    }

    public function create(User $user): bool
    {
        return $user->can('create bills');
    }

    public function processPayment(User $user, Bill $bill): bool
    {
        return $user->can('process payments');
    }

    public function update(User $user, Bill $bill): bool
    {
        return $user->can('process payments');
    }

    public function delete(User $user, Bill $bill): bool
    {
        // Typically only admin can delete bills
        return $user->hasRole('admin');
    }
}

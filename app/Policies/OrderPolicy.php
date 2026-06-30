<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view orders');
    }

    public function view(User $user, Order $order): bool
    {
        return $user->can('view orders');
    }

    public function create(User $user): bool
    {
        return $user->can('create orders');
    }

    public function update(User $user, Order $order): bool
    {
        return $user->can('update order status');
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('admin');
    }
}

<?php

namespace App\Policies;

use App\Models\RestaurantTable;
use App\Models\User;

class RestaurantTablePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view tables');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RestaurantTable $restaurantTable): bool
    {
        return $user->can('view tables');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage tables');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RestaurantTable $restaurantTable): bool
    {
        return $user->can('manage tables');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RestaurantTable $restaurantTable): bool
    {
        return $user->can('manage tables');
    }
    
    /**
     * Determine whether the user can update the table status.
     */
    public function updateStatus(User $user, RestaurantTable $restaurantTable): bool
    {
        return $user->can('update table status');
    }
}

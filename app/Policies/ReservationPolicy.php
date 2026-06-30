<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view reservations');
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->can('view reservations');
    }

    public function create(User $user): bool
    {
        return $user->can('create reservations');
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->can('update reservation status');
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('admin');
    }
}

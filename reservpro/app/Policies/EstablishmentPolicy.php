<?php

namespace App\Policies;

use App\Models\Establishment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EstablishmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Establishment $establishment): bool
{
    return $establishment->is_published || $user->id === $establishment->owner_id;
}

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
{
    // Seul un gérant ou un administrateur (Admin) peut créer un établissement
    return $user->role === \App\Enums\UserRole::Gerant
        || $user->role === \App\Enums\UserRole::Admin;
}

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Establishment $establishment): bool
    {
        return $user->id === $establishment->owner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Establishment $establishment): bool
{
    return $user->id === $establishment->owner_id
        || $user->role === \App\Enums\UserRole::Admin;
}

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Establishment $establishment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Establishment $establishment): bool
    {
        return false;
    }
}

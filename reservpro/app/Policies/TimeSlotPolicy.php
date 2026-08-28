<?php

namespace App\Policies;

use App\Models\Establishment;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TimeSlotPolicy
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
    public function view(User $user, TimeSlot $timeSlot): bool
    {
        return $timeSlot->establishment->is_published
            || $user->id === $timeSlot->establishment->owner_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Establishment $establishment): bool
    {
        return $user->id === $establishment->owner_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TimeSlot $timeSlot): bool
    {
        return $user->id === $timeSlot->establishment->owner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TimeSlot $timeSlot): bool
    {
        return $user->id === $timeSlot->establishment->owner_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TimeSlot $timeSlot): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TimeSlot $timeSlot): bool
    {
        return false;
    }
}

<?php

namespace App\Policies;

use App\Models\Exemption;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExemptionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'employee';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Exemption $exemption): bool
    {
        return $user->role === 'admin' || $user->role === 'employee';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'employee';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Exemption $exemption): bool
    {
        return $user->role === 'admin' || $user->role === 'employee';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Exemption $exemption): bool
    {
        return $user->role === 'admin' || $user->role === 'employee';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Exemption $exemption): bool
    {
        return $user->role === 'admin' || $user->role === 'employee';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Exemption $exemption): bool
    {
        return $user->role === 'admin' || $user->role === 'employee';
    }

    public function changeStatus(User $user, Exemption $exemption): bool
    {
        return $user->role === 'admin' || $user->role === 'supervisor';
    }
}

<?php

namespace App\Policies;

use App\Unit;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UnitPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any units.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the unit.
     *
     * @param  \App\User  $user
     * @param  \App\Unit  $unit
     * @return mixed
     */
    public function view(User $user, Unit $unit)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can create units.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the unit.
     *
     * @param  \App\User  $user
     * @param  \App\Unit  $unit
     * @return mixed
     */
    public function update(User $user, Unit $unit)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can delete the unit.
     *
     * @param  \App\User  $user
     * @param  \App\Unit  $unit
     * @return mixed
     */
    public function delete(User $user, Unit $unit)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can restore the unit.
     *
     * @param  \App\User  $user
     * @param  \App\Unit  $unit
     * @return mixed
     */
    public function restore(User $user, Unit $unit)
    {
        return $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the unit.
     *
     * @param  \App\User  $user
     * @param  \App\Unit  $unit
     * @return mixed
     */
    public function forceDelete(User $user, Unit $unit)
    {
        return $user->roles == 'HELPDESK';
    }
}

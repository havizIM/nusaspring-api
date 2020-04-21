<?php

namespace App\Policies;

use App\Adjustment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdjustmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any adjustments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the adjustment.
     *
     * @param  \App\User  $user
     * @param  \App\Adjustment  $adjustment
     * @return mixed
     */
    public function view(User $user, Adjustment $adjustment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can create adjustments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the adjustment.
     *
     * @param  \App\User  $user
     * @param  \App\Adjustment  $adjustment
     * @return mixed
     */
    public function update(User $user, Adjustment $adjustment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can delete the adjustment.
     *
     * @param  \App\User  $user
     * @param  \App\Adjustment  $adjustment
     * @return mixed
     */
    public function delete(User $user, Adjustment $adjustment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can restore the adjustment.
     *
     * @param  \App\User  $user
     * @param  \App\Adjustment  $adjustment
     * @return mixed
     */
    public function restore(User $user, Adjustment $adjustment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the adjustment.
     *
     * @param  \App\User  $user
     * @param  \App\Adjustment  $adjustment
     * @return mixed
     */
    public function forceDelete(User $user, Adjustment $adjustment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }
}

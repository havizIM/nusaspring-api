<?php

namespace App\Policies;

use App\Selling;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any sellings.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the selling.
     *
     * @param  \App\User  $user
     * @param  \App\Selling  $selling
     * @return mixed
     */
    public function view(User $user, Selling $selling)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can create sellings.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the selling.
     *
     * @param  \App\User  $user
     * @param  \App\Selling  $selling
     * @return mixed
     */
    public function update(User $user, Selling $selling)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can delete the selling.
     *
     * @param  \App\User  $user
     * @param  \App\Selling  $selling
     * @return mixed
     */
    public function delete(User $user, Selling $selling)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can restore the selling.
     *
     * @param  \App\User  $user
     * @param  \App\Selling  $selling
     * @return mixed
     */
    public function restore(User $user, Selling $selling)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the selling.
     *
     * @param  \App\User  $user
     * @param  \App\Selling  $selling
     * @return mixed
     */
    public function forceDelete(User $user, Selling $selling)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }
}

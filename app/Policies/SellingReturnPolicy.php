<?php

namespace App\Policies;

use App\SellingReturn;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellingReturnPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any selling returns.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the selling return.
     *
     * @param  \App\User  $user
     * @param  \App\SellingReturn  $sellingReturn
     * @return mixed
     */
    public function view(User $user, SellingReturn $sellingReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can create selling returns.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the selling return.
     *
     * @param  \App\User  $user
     * @param  \App\SellingReturn  $sellingReturn
     * @return mixed
     */
    public function update(User $user, SellingReturn $sellingReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can delete the selling return.
     *
     * @param  \App\User  $user
     * @param  \App\SellingReturn  $sellingReturn
     * @return mixed
     */
    public function delete(User $user, SellingReturn $sellingReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can restore the selling return.
     *
     * @param  \App\User  $user
     * @param  \App\SellingReturn  $sellingReturn
     * @return mixed
     */
    public function restore(User $user, SellingReturn $sellingReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the selling return.
     *
     * @param  \App\User  $user
     * @param  \App\SellingReturn  $sellingReturn
     * @return mixed
     */
    public function forceDelete(User $user, SellingReturn $sellingReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }
}

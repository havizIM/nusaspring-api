<?php

namespace App\Policies;

use App\StockOpname;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockOpnamePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\StockOpname  $stockOpname
     * @return mixed
     */
    public function view(User $user, StockOpname $stockOpname)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\StockOpname  $stockOpname
     * @return mixed
     */
    public function update(User $user, StockOpname $stockOpname)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\StockOpname  $stockOpname
     * @return mixed
     */
    public function delete(User $user, StockOpname $stockOpname)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\StockOpname  $stockOpname
     * @return mixed
     */
    public function restore(User $user, StockOpname $stockOpname)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\StockOpname  $stockOpname
     * @return mixed
     */
    public function forceDelete(User $user, StockOpname $stockOpname)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }
}

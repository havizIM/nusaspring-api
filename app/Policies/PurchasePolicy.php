<?php

namespace App\Policies;

use App\Purchase;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any purchases.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the purchase.
     *
     * @param  \App\User  $user
     * @param  \App\Purchase  $purchase
     * @return mixed
     */
    public function view(User $user, Purchase $purchase)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can create purchases.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the purchase.
     *
     * @param  \App\User  $user
     * @param  \App\Purchase  $purchase
     * @return mixed
     */
    public function update(User $user, Purchase $purchase)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can delete the purchase.
     *
     * @param  \App\User  $user
     * @param  \App\Purchase  $purchase
     * @return mixed
     */
    public function delete(User $user, Purchase $purchase)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can restore the purchase.
     *
     * @param  \App\User  $user
     * @param  \App\Purchase  $purchase
     * @return mixed
     */
    public function restore(User $user, Purchase $purchase)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the purchase.
     *
     * @param  \App\User  $user
     * @param  \App\Purchase  $purchase
     * @return mixed
     */
    public function forceDelete(User $user, Purchase $purchase)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }
}

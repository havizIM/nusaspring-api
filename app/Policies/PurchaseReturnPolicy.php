<?php

namespace App\Policies;

use App\PurchaseReturn;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseReturnPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any purchase returns.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the purchase return.
     *
     * @param  \App\User  $user
     * @param  \App\PurchaseReturn  $purchaseReturn
     * @return mixed
     */
    public function view(User $user, PurchaseReturn $purchaseReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can create purchase returns.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the purchase return.
     *
     * @param  \App\User  $user
     * @param  \App\PurchaseReturn  $purchaseReturn
     * @return mixed
     */
    public function update(User $user, PurchaseReturn $purchaseReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can delete the purchase return.
     *
     * @param  \App\User  $user
     * @param  \App\PurchaseReturn  $purchaseReturn
     * @return mixed
     */
    public function delete(User $user, PurchaseReturn $purchaseReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can restore the purchase return.
     *
     * @param  \App\User  $user
     * @param  \App\PurchaseReturn  $purchaseReturn
     * @return mixed
     */
    public function restore(User $user, PurchaseReturn $purchaseReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the purchase return.
     *
     * @param  \App\User  $user
     * @param  \App\PurchaseReturn  $purchaseReturn
     * @return mixed
     */
    public function forceDelete(User $user, PurchaseReturn $purchaseReturn)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }
}

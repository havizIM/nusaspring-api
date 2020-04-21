<?php

namespace App\Policies;

use App\PurchasePayment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasePaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any purchase payments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the purchase payment.
     *
     * @param  \App\User  $user
     * @param  \App\PurchasePayment  $purchasePayment
     * @return mixed
     */
    public function view(User $user, PurchasePayment $purchasePayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can create purchase payments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the purchase payment.
     *
     * @param  \App\User  $user
     * @param  \App\PurchasePayment  $purchasePayment
     * @return mixed
     */
    public function update(User $user, PurchasePayment $purchasePayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can delete the purchase payment.
     *
     * @param  \App\User  $user
     * @param  \App\PurchasePayment  $purchasePayment
     * @return mixed
     */
    public function delete(User $user, PurchasePayment $purchasePayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can restore the purchase payment.
     *
     * @param  \App\User  $user
     * @param  \App\PurchasePayment  $purchasePayment
     * @return mixed
     */
    public function restore(User $user, PurchasePayment $purchasePayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the purchase payment.
     *
     * @param  \App\User  $user
     * @param  \App\PurchasePayment  $purchasePayment
     * @return mixed
     */
    public function forceDelete(User $user, PurchasePayment $purchasePayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }
}

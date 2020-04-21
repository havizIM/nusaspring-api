<?php

namespace App\Policies;

use App\SellingPayment;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SellingPaymentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any selling payments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the selling payment.
     *
     * @param  \App\User  $user
     * @param  \App\SellingPayment  $sellingPayment
     * @return mixed
     */
    public function view(User $user, SellingPayment $sellingPayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can create selling payments.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the selling payment.
     *
     * @param  \App\User  $user
     * @param  \App\SellingPayment  $sellingPayment
     * @return mixed
     */
    public function update(User $user, SellingPayment $sellingPayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can delete the selling payment.
     *
     * @param  \App\User  $user
     * @param  \App\SellingPayment  $sellingPayment
     * @return mixed
     */
    public function delete(User $user, SellingPayment $sellingPayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can restore the selling payment.
     *
     * @param  \App\User  $user
     * @param  \App\SellingPayment  $sellingPayment
     * @return mixed
     */
    public function restore(User $user, SellingPayment $sellingPayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the selling payment.
     *
     * @param  \App\User  $user
     * @param  \App\SellingPayment  $sellingPayment
     * @return mixed
     */
    public function forceDelete(User $user, SellingPayment $sellingPayment)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }
}

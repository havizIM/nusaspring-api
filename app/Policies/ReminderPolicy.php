<?php

namespace App\Policies;

use App\Reminder;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReminderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any reminders.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can view the reminder.
     *
     * @param  \App\User  $user
     * @param  \App\Reminder  $reminder
     * @return mixed
     */
    public function view(User $user, Reminder $reminder)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK' && $reminder->user_id == $user->id;
    }

    /**
     * Determine whether the user can create reminders.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can update the reminder.
     *
     * @param  \App\User  $user
     * @param  \App\Reminder  $reminder
     * @return mixed
     */
    public function update(User $user, Reminder $reminder)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK' && $reminder->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the reminder.
     *
     * @param  \App\User  $user
     * @param  \App\Reminder  $reminder
     * @return mixed
     */
    public function delete(User $user, Reminder $reminder)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK' && $reminder->user_id == $user->id;
    }

    /**
     * Determine whether the user can restore the reminder.
     *
     * @param  \App\User  $user
     * @param  \App\Reminder  $reminder
     * @return mixed
     */
    public function restore(User $user, Reminder $reminder)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }

    /**
     * Determine whether the user can permanently delete the reminder.
     *
     * @param  \App\User  $user
     * @param  \App\Reminder  $reminder
     * @return mixed
     */
    public function forceDelete(User $user, Reminder $reminder)
    {
        return $user->roles == 'ADMIN' || $user->roles == 'HELPDESK';
    }
}

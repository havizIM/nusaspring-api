<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Category' => 'App\Policies\CategoryPolicy',
        'App\Unit' => 'App\Policies\UnitPolicy',
        'App\Task' => 'App\Policies\TaskPolicy',
        'App\Reminder' => 'App\Policies\ReminderPolicy',
        'App\Contact' => 'App\Policies\ContactPolicy',
        'App\Product' => 'App\Policies\ProductPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}

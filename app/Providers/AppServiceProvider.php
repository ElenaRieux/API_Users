<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // User
        Gate::define('isAdmin', [UserPolicy::class, 'isAdmin']);
        Gate::define('canUpdateUser', [UserPolicy::class, 'canUpdate']);
        Gate::define('canReadUser', [UserPolicy::class, 'canRead']);
        Gate::define('canCreateUser', [UserPolicy::class, 'canCreate']);
        Gate::define('canDeleteUser', [UserPolicy::class, 'canDelete']);
        Gate::define('loggedUserUser', [UserPolicy::class, 'loggedUser']);

        // Role

        Gate::define('canUpdateRole', [RolePolicy::class, 'canUpdate']);
        Gate::define('canReadRole', [RolePolicy::class, 'canRead']);
        Gate::define('canCreateRole', [RolePolicy::class, 'canCreate']);
        Gate::define('canDeleteRole', [RolePolicy::class, 'canDelete']);
    }
}

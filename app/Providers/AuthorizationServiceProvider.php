<?php

namespace App\Providers;

use App\Support\Permissions;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Every permission slug becomes a Gate, so `@can('billing.manage')` works
        // in Blade and `$this->authorize(...)` works in controllers.
        foreach (Permissions::flat() as $permission) {
            Gate::define($permission['slug'], fn ($user) => $user->hasPermission($permission['slug']));
        }

        Gate::before(fn ($user) => $user->isOwner() ? true : null);
    }
}

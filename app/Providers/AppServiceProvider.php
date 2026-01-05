<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // ...
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // BYPASS GLOBAL: si es SuperAdmin, puede TODO
        Gate::before(function ($user, $ability) {
            try {
                // Spatie: hasRole()
                if (method_exists($user, 'hasRole') && $user->hasRole('SuperAdmin')) {
                    return true;
                }
            } catch (\Throwable $e) {
                // no hacemos nada, seguimos normal
            }
            return null;
        });
    }
}

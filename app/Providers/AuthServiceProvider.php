<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        // Importante: registrar policies si usas policies
        $this->registerPolicies();

        // BYPASS: si es SuperAdmin, pasa cualquier permiso/ability
        Gate::before(function ($user, $ability) {

            // 1) Si usas Spatie Roles:
            if ($user && method_exists($user, 'hasRole') && $user->hasRole('SuperAdmin')) {
                return true;
            }

            // 2) Si además tienes columna booleana:
            if ($user && !empty($user->is_superadmin)) {
                return true;
            }

            // 🔁 si no aplica, sigue evaluación normal
            return null;
        });
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $u = auth()->user();
        if (!$u) abort(401);

        // por columna
        if (!empty($u->is_superadmin)) return $next($request);

        // por rol (Spatie)
        if (method_exists($u, 'getRoleNames') && $u->getRoleNames()->contains('SuperAdmin')) {
            return $next($request);
        }

        if (method_exists($u, 'hasRole') && $u->hasRole('SuperAdmin')) {
            return $next($request);
        }

        abort(403, 'User does not have the right permissions.');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOrSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $u = auth()->user();
        if (!$u) abort(401);

        // ✅ SuperAdmin por columna
        if (!empty($u->is_superadmin)) {
            return $next($request);
        }

        // ✅ SuperAdmin por rol (robusto: getRoleNames ignora muchos líos de guard)
        if (method_exists($u, 'getRoleNames')) {
            if ($u->getRoleNames()->contains('SuperAdmin')) {
                return $next($request);
            }
        } else {
            // fallback por si no está Spatie bien cargado
            if (method_exists($u, 'hasRole') && $u->hasRole('SuperAdmin')) {
                return $next($request);
            }
        }

        // ✅ Admin del sistema por permiso
        if (method_exists($u, 'can') && $u->can('admin.ver')) {
            return $next($request);
        }

        abort(403, 'User does not have the right permissions.');
    }
}

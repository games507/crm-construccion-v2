<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmpresaContext
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Solo aplica si está logueado
        if (!$user) return $next($request);

        // Detectar super admin (Spatie o columna)
        $isSuperAdmin = false;
        if (method_exists($user, 'hasRole')) {
            $isSuperAdmin = $user->hasRole('SuperAdmin');
        } elseif (isset($user->is_superadmin)) {
            $isSuperAdmin = (bool) $user->is_superadmin;
        }

        // Si es Super Admin, permitimos contexto por sesión
        if ($isSuperAdmin) {
            // Si hay empresa en sesión, la inyectamos al request para que tus controllers la usen
            $ctx = (int) session('empresa_ctx_id', 0);
            if ($ctx > 0) {
                // No modificamos el user en DB, solo contexto de request
                $request->attributes->set('empresa_ctx_id', $ctx);
            }
        }

        return $next($request);
    }
}

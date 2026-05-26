<?php

namespace App\Http\Middleware;

use App\Models\Empresa;
use Closure;
use Illuminate\Http\Request;

class EmpresaContext
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        $isSuperAdmin = false;

        if (method_exists($user, 'hasRole') && $user->hasRole('SuperAdmin')) {
            $isSuperAdmin = true;
        }

        if ((bool) ($user->is_superadmin ?? false)) {
            $isSuperAdmin = true;
        }

        if (!$isSuperAdmin) {
            session()->forget('empresa_ctx_id');
            $request->attributes->remove('empresa_ctx_id');

            return $next($request);
        }

        $ctx = (int) session('empresa_ctx_id', 0);

        if ($ctx > 0) {
            $empresaExiste = Empresa::where('id', $ctx)->exists();

            if ($empresaExiste) {
                $request->attributes->set('empresa_ctx_id', $ctx);
            } else {
                session()->forget('empresa_ctx_id');
                $request->attributes->remove('empresa_ctx_id');
            }
        }

        return $next($request);
    }
}
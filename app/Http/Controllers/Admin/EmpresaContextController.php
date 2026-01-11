<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;

class EmpresaContextController extends Controller
{
    private function isSuperAdmin($user): bool
    {
        if (!$user) return false;

        // ✅ Spatie: revisa ambos nombres por si acaso
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('SuperAdmin')) return true;     // recomendado
            if ($user->hasRole('Super Admin')) return true;    // por compatibilidad
        }

        // ✅ Flag alterno (si existe)
        if (isset($user->is_superadmin) && (bool)$user->is_superadmin) {
            return true;
        }

        return false;
    }

    public function set(Request $request)
    {
        $user = auth()->user();

        // ✅ Solo Super Admin
        if (!$this->isSuperAdmin($user)) {
            abort(403, 'No autorizado.');
        }

        $data = $request->validate([
            'empresa_id' => ['required', 'integer', 'exists:empresas,id'],
        ], [
            'empresa_id.required' => 'Seleccione una empresa.',
            'empresa_id.exists'   => 'La empresa seleccionada no existe.',
        ]);

        // Validación extra opcional
        Empresa::whereKey((int)$data['empresa_id'])->firstOrFail();

        // ✅ Set en sesión (EmpresaScope usa session)
        EmpresaScope::set((int) $data['empresa_id']);

        return back()->with('ok', 'Contexto de empresa actualizado.');
    }

    public function clear()
    {
        $user = auth()->user();

        // ✅ Solo Super Admin
        if (!$this->isSuperAdmin($user)) {
            abort(403, 'No autorizado.');
        }

        EmpresaScope::clear();

        return back()->with('ok', 'Contexto global restaurado (todas las empresas).');
    }
}

<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Almacen;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Support\EmpresaScope;

class AlmacenController extends Controller
{
    /**
     * Detecta si el usuario es SuperAdmin
     */
    private function isSuperAdmin(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        // Spatie
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('SuperAdmin');
        }

        // Columna booleana
        return (bool) ($user->is_superadmin ?? false);
    }

    /**
     * Obtiene empresa_id:
     * - Usuario normal => user->empresa_id
     * - SuperAdmin => EmpresaScope::getId() (obligatorio)
     */
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        if ($this->isSuperAdmin()) {
            if (!EmpresaScope::has()) {
                abort(403, 'Super Admin: selecciona una empresa (contexto) para trabajar Inventario.');
            }

            $empresaId = (int) EmpresaScope::getId();
            if ($empresaId <= 0) {
                abort(403, 'Super Admin: contexto de empresa inválido. Selecciona una empresa nuevamente.');
            }

            return $empresaId;
        }

        $empresaId = (int) ($user->empresa_id ?? 0);
        if ($empresaId <= 0) {
            abort(403, 'Tu usuario no tiene empresa asignada.');
        }

        return $empresaId;
    }

    /**
     * Listado
     */
    public function index(Request $r)
    {
        $empresaId = $this->empresaIdOrAbort();
        $q = trim((string) $r->get('q', ''));

        $almacenes = Almacen::query()
            ->where('empresa_id', $empresaId)
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('codigo', 'like', "%{$q}%")
                        ->orWhere('nombre', 'like', "%{$q}%")
                        ->orWhere('ubicacion', 'like', "%{$q}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('inventario.almacenes.index', compact('almacenes', 'q'));
    }

    /**
     * Crear
     */
    public function create()
    {
        $this->empresaIdOrAbort();
        return view('inventario.almacenes.create');
    }

    /**
     * Guardar nuevo
     */
    public function store(Request $r)
    {
        $empresaId = $this->empresaIdOrAbort();

        $data = $r->validate([
            'codigo' => [
                'required',
                'string',
                'max:30',
                // ✅ ÚNICO POR EMPRESA (sin el unique global)
                Rule::unique('almacenes', 'codigo')
                    ->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'nombre'    => ['required', 'string', 'max:120'],
            'ubicacion' => ['nullable', 'string', 'max:200'],
            'activo'    => ['nullable', 'in:0,1'],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique'   => 'Ese código ya existe en tu empresa.',
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        $data['empresa_id'] = $empresaId;

        // ✅ Si viene como checkbox/switch:
        // - si no viene => 0
        // - si viene => 1
        $data['activo'] = (int) $r->boolean('activo', true);

        Almacen::create($data);

        return redirect()
            ->route('inventario.almacenes')
            ->with('ok', 'Almacén creado correctamente.');
    }

    /**
     * Editar
     */
    public function edit(Almacen $almacen)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $almacen->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este almacén.');
        }

        return view('inventario.almacenes.edit', compact('almacen'));
    }

    /**
     * Actualizar
     */
    public function update(Request $r, Almacen $almacen)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $almacen->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este almacén.');
        }

        $data = $r->validate([
            'codigo' => [
                'required',
                'string',
                'max:30',
                Rule::unique('almacenes', 'codigo')
                    ->where(fn ($q) => $q->where('empresa_id', $empresaId))
                    ->ignore($almacen->id),
            ],
            'nombre'    => ['required', 'string', 'max:120'],
            'ubicacion' => ['nullable', 'string', 'max:200'],
            'activo'    => ['nullable', 'in:0,1'],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique'   => 'Ese código ya existe en tu empresa.',
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        $data['activo'] = (int) $r->boolean('activo', false);

        $almacen->update($data);

        return redirect()
            ->route('inventario.almacenes')
            ->with('ok', 'Almacén actualizado correctamente.');
    }

    /**
     * Eliminar / Desactivar
     */
    public function destroy(Almacen $almacen)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $almacen->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este almacén.');
        }

        // ✅ Multiempresa: filtrar por empresa_id
        $tieneExistencias = DB::table('inv_existencias')
            ->where('empresa_id', $empresaId)
            ->where('almacen_id', $almacen->id)
            ->exists();

        if ($tieneExistencias) {
            $almacen->update(['activo' => 0]);

            return redirect()
                ->route('inventario.almacenes')
                ->with('err', 'Este almacén tiene existencias; se marcó como INACTIVO.');
        }

        try {
            $almacen->delete();

            return redirect()
                ->route('inventario.almacenes')
                ->with('ok', 'Almacén eliminado.');
        } catch (QueryException $e) {
            $almacen->update(['activo' => 0]);

            return redirect()
                ->route('inventario.almacenes')
                ->with('err', 'No se pudo eliminar por dependencias. Se marcó como INACTIVO.');
        }
    }

    /**
     * Desactivar manual
     */
    public function deactivate(Almacen $almacen)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $almacen->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este almacén.');
        }

        $almacen->update(['activo' => 0]);

        return redirect()
            ->route('inventario.almacenes')
            ->with('ok', 'Almacén desactivado.');
    }
}

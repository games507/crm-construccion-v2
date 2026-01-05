<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Almacen;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class AlmacenController extends Controller
{
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();
        $empresaId = (int) ($user->empresa_id ?? 0);

        if ($empresaId <= 0) {
            abort(403, 'Tu usuario no tiene empresa asignada.');
        }

        return $empresaId;
    }

    public function index(Request $r)
    {
        $empresaId = $this->empresaIdOrAbort();
        $q = trim((string) $r->get('q', ''));

        $almacenesQ = Almacen::query()
            ->where('empresa_id', $empresaId)
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('codigo', 'like', "%{$q}%")
                      ->orWhere('nombre', 'like', "%{$q}%")
                      ->orWhere('ubicacion', 'like', "%{$q}%");
                });
            })
            ->orderBy('nombre');

        $almacenes = $almacenesQ->paginate(15)->withQueryString();

        return view('inventario.almacenes.index', compact('almacenes', 'q'));
    }

    public function create()
    {
        $this->empresaIdOrAbort();
        return view('inventario.almacenes.create');
    }

    public function store(Request $r)
    {
        $empresaId = $this->empresaIdOrAbort();

        $data = $r->validate([
            'codigo' => [
                'required', 'string', 'max:30',
                Rule::unique('almacenes', 'codigo')
                    ->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'nombre'    => ['required', 'string', 'max:120'],
            'ubicacion' => ['nullable', 'string', 'max:200'],
            'activo'    => ['nullable'], // checkbox
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique'   => 'Ese código ya existe en tu empresa.',
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        $data['empresa_id'] = $empresaId;

        // ✅ Si quieres que "nuevo almacén" sea Activo por defecto:
        // - Si el input no viene, lo ponemos en 1 (activo).
        $data['activo'] = $r->has('activo') ? 1 : 1;

        Almacen::create($data);

        return redirect()
            ->route('inventario.almacenes')
            ->with('ok', 'Almacén creado correctamente.');
    }

    public function edit(Almacen $almacen)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $almacen->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este almacén.');
        }

        return view('inventario.almacenes.edit', compact('almacen'));
    }

    public function update(Request $r, Almacen $almacen)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $almacen->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este almacén.');
        }

        $data = $r->validate([
            'codigo' => [
                'required', 'string', 'max:30',
                Rule::unique('almacenes', 'codigo')
                    ->ignore($almacen->id)
                    ->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'nombre'    => ['required', 'string', 'max:120'],
            'ubicacion' => ['nullable', 'string', 'max:200'],
            'activo'    => ['nullable'], // checkbox
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique'   => 'Ese código ya existe en tu empresa.',
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        // ✅ Checkbox: si viene => 1, si no viene => 0
        $data['activo'] = $r->has('activo') ? 1 : 0;

        $almacen->update($data);

        return redirect()
            ->route('inventario.almacenes')
            ->with('ok', 'Almacén actualizado correctamente.');
    }

    public function destroy(Almacen $almacen)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $almacen->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este almacén.');
        }

        // ✅ Regla viable sin romper:
        // Si está referenciado por existencias o movimientos => NO borrar, desactivar.

        $tieneExistencias = DB::table('inv_existencias')
            ->where('almacen_id', $almacen->id)
            ->exists();

        // Si tienes movimientos con FK a almacén, activa esto con el nombre real de tu tabla/columna:
        // $tieneMovimientos = DB::table('inv_movimientos')->where('almacen_id', $almacen->id)->exists();
        $tieneMovimientos = false;

        if ($tieneExistencias || $tieneMovimientos) {
            $almacen->update(['activo' => 0]);

            return redirect()
                ->route('inventario.almacenes')
                ->with('err', 'Este almacén tiene registros asociados; por seguridad NO se elimina. Se marcó como INACTIVO.');
        }

        try {
            $almacen->delete();

            return redirect()
                ->route('inventario.almacenes')
                ->with('ok', 'Almacén eliminado.');
        } catch (QueryException $e) {
            // ✅ Por si existe otra FK no contemplada:
            $almacen->update(['activo' => 0]);

            return redirect()
                ->route('inventario.almacenes')
                ->with('err', 'No se pudo eliminar por dependencias. Se marcó como INACTIVO.');
        }
    }

    public function deactivate(Almacen $almacen)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $almacen->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este almacén.');
        }

        $almacen->update(['activo' => 0]);

        return redirect()
            ->route('inventario.almacenes')
            ->with('ok', 'Almacén desactivado (no se eliminó).');
    }
}

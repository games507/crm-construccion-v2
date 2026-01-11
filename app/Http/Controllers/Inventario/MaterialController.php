<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Material;
use App\Models\Unidad;
use Illuminate\Database\QueryException;
use App\Support\EmpresaScope;

class MaterialController extends Controller
{
    /**
     * PATRÓN ÚNICO PARA EMPRESA (CÓPIALO EN LOS DEMÁS)
     *
     * 1) Si hay contexto seleccionado (EmpresaScope::getId) -> úsalo (Super Admin)
     * 2) Si no hay contexto -> usa empresa_id del usuario (usuario normal)
     * 3) Si ambos son 0 -> 403 (mensaje claro)
     *
     * OJO: así NO dependes del nombre exacto del rol ("Super Admin" vs "SuperAdmin").
     */
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        $scopeEmpresaId = (int) EmpresaScope::getId();        // super admin cuando elige empresa
        $userEmpresaId  = (int) ($user->empresa_id ?? 0);     // usuario normal

        $empresaId = $scopeEmpresaId > 0 ? $scopeEmpresaId : $userEmpresaId;

        if ($empresaId <= 0) {
            abort(403, 'Seleccione una empresa para continuar.');
        }

        return $empresaId;
    }

    private function unidadesList()
    {
        return Unidad::query()
            ->orderBy('descripcion')
            ->get(['id','codigo','descripcion']);
    }

    private function unidadTextoFromId(int $unidadId): string
    {
        $u = Unidad::query()->whereKey($unidadId)->first(['id','codigo','descripcion']);
        if (!$u) abort(422, 'Unidad inválida.');
        return (string) $u->descripcion; // o $u->codigo si prefieres
    }

    public function index(Request $r)
    {
        $empresaId = $this->empresaIdOrAbort();
        $q = trim((string) $r->get('q', ''));

        $itemsQ = Material::query()
            ->where('empresa_id', $empresaId)
            ->orderBy('descripcion');

        if ($q !== '') {
            $itemsQ->where(function ($qq) use ($q) {
                $qq->where('descripcion', 'like', "%{$q}%")
                   ->orWhere('codigo', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%")
                   ->orWhere('unidad', 'like', "%{$q}%");
            });
        }

        $materiales = $itemsQ->paginate(15)->withQueryString();

        return view('inventario.materiales.index', compact('materiales', 'q'));
    }

    public function create()
    {
        $this->empresaIdOrAbort();
        $unidades = $this->unidadesList();

        return view('inventario.materiales.create', compact('unidades'));
    }

    public function store(Request $r)
    {
        $empresaId = $this->empresaIdOrAbort();

        $data = $r->validate([
            'codigo' => [
                'required','string','max:50',
                Rule::unique('materiales','codigo')
                    ->where(fn($q) => $q->where('empresa_id',$empresaId))
            ],
            'descripcion' => [
                'required','string','max:200',
                Rule::unique('materiales','descripcion')
                    ->where(fn($q) => $q->where('empresa_id',$empresaId))
            ],
            'unidad_id' => ['required','integer','exists:unidades,id'],
            'costo_estandar' => ['nullable','numeric','min:0'],
            'activo' => ['nullable','in:0,1'],
        ], [
            'codigo.required' => 'El código es obligatorio.',
            'codigo.unique' => 'Ese código ya existe en tu empresa.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.unique' => 'Ya existe un material con esa descripción en tu empresa.',
            'unidad_id.required' => 'La unidad es obligatoria.',
        ]);

        $codigo = trim((string)$data['codigo']);

        $data['unidad'] = $this->unidadTextoFromId((int)$data['unidad_id']);
        $data['sku'] = 'E' . $empresaId . '-' . $codigo;
        $data['empresa_id'] = $empresaId;

        // ACTIVO real (por defecto 1 al crear)
        $data['activo'] = (int) $r->input('activo', 1);

        // costo a 2 decimales
        $data['costo_estandar'] = isset($data['costo_estandar'])
            ? round((float)$data['costo_estandar'], 2)
            : 0;

        // evita colisión global de sku
        $skuExists = Material::query()->where('sku', $data['sku'])->exists();
        if ($skuExists) {
            $data['sku'] = $data['sku'] . '-' . strtoupper(substr(uniqid(), -4));
        }

        Material::create($data);

        return redirect()
            ->route('inventario.materiales')
            ->with('ok', 'Material creado correctamente.');
    }

    public function edit(Material $material)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int)$material->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este material.');
        }

        $unidades = $this->unidadesList();

        return view('inventario.materiales.edit', compact('material','unidades'));
    }

    public function update(Request $r, Material $material)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int)$material->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este material.');
        }

        $data = $r->validate([
            'codigo' => [
                'required','string','max:50',
                Rule::unique('materiales','codigo')
                    ->ignore($material->id)
                    ->where(fn($q) => $q->where('empresa_id',$empresaId))
            ],
            'descripcion' => [
                'required','string','max:200',
                Rule::unique('materiales','descripcion')
                    ->ignore($material->id)
                    ->where(fn($q) => $q->where('empresa_id',$empresaId))
            ],
            'unidad_id' => ['required','integer','exists:unidades,id'],
            'costo_estandar' => ['nullable','numeric','min:0'],
            'activo' => ['required','in:0,1'], // requiere 0/1 (usa hidden + checkbox en blade)
        ]);

        $codigo = trim((string)$data['codigo']);

        $data['unidad'] = $this->unidadTextoFromId((int)$data['unidad_id']);

        // SKU por empresa + código
        $nuevoSku = 'E' . $empresaId . '-' . $codigo;

        $skuExists = Material::query()
            ->where('sku', $nuevoSku)
            ->where('id', '!=', $material->id)
            ->exists();

        if ($skuExists) {
            $nuevoSku = $nuevoSku . '-' . strtoupper(substr(uniqid(), -4));
        }

        $data['sku'] = $nuevoSku;

        // ACTIVO real
        $data['activo'] = (int) $r->input('activo', 0);

        // costo a 2 decimales
        $data['costo_estandar'] = isset($data['costo_estandar'])
            ? round((float)$data['costo_estandar'], 2)
            : 0;

        $material->update($data);

        return redirect()
            ->route('inventario.materiales')
            ->with('ok', 'Material actualizado correctamente.');
    }

    public function destroy(Material $material)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int)$material->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este material.');
        }

        try {
            $material->delete();
            return redirect()->route('inventario.materiales')->with('ok', 'Material eliminado.');
        } catch (QueryException $e) {
            $material->update(['activo' => 0]);

            return redirect()
                ->route('inventario.materiales')
                ->with('err', 'No se pudo eliminar porque tiene movimientos/existencias asociadas. Se marcó como INACTIVO.');
        }
    }
}

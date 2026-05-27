<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Material;
use App\Models\Unidad;
use App\Support\EmpresaScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        $empresaId = (int) EmpresaScope::getId() ?: (int) ($user->empresa_id ?? 0);

        if ($empresaId <= 0) {
            abort(403, 'Seleccione una empresa para continuar.');
        }

        return $empresaId;
    }

    private function unidadesList()
    {
        $q = Unidad::query();

        if (Schema::hasColumn('unidades', 'activo')) {
            $q->where('activo', 1);
        }

        return $q->orderBy('descripcion')->get(['id', 'codigo', 'descripcion']);
    }

    private function unidadTextoFromId(int $unidadId): string
    {
        $u = Unidad::query()->whereKey($unidadId)->first();

        if (!$u) {
            abort(422, 'Unidad inválida.');
        }

        return (string) ($u->descripcion ?? $u->codigo ?? 'Unidad');
    }

    public function index(Request $r)
    {
        $empresaId = $this->empresaIdOrAbort();
        $q = trim((string) $r->get('q', ''));

        $query = Material::query()
            ->where('empresa_id', $empresaId)
            ->orderBy('descripcion');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('descripcion', 'like', "%{$q}%")
                   ->orWhere('codigo', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%")
                   ->orWhere('unidad', 'like', "%{$q}%");
            });
        }

        $items = $query->paginate(20)->withQueryString();

        return view('inventario.materiales.index', [
            'items' => $items,
            'q' => $q,
        ]);
    }

    public function pdf(Request $r)
    {
        $empresaId = $this->empresaIdOrAbort();
        $q = trim((string) $r->get('q', ''));

        $query = Material::query()
            ->where('empresa_id', $empresaId)
            ->orderBy('descripcion');

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('descripcion', 'like', "%{$q}%")
                   ->orWhere('codigo', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%")
                   ->orWhere('unidad', 'like', "%{$q}%");
            });
        }

        $pdf = Pdf::loadView('inventario.materiales.pdf', [
            'empresa' => Empresa::find($empresaId),
            'materiales' => $query->get(),
            'q' => $q,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('materiales.pdf');
    }

    public function create()
    {
        $this->empresaIdOrAbort();

        $unidades = $this->unidadesList();

        if ($unidades->isEmpty()) {
            return redirect()
                ->route('inventario.materiales')
                ->with('err', 'No hay unidades registradas. Debes crear al menos una unidad antes de crear materiales.');
        }

        return view('inventario.materiales.create', compact('unidades'));
    }

    public function store(Request $r)
    {
        $empresaId = $this->empresaIdOrAbort();

        $data = $r->validate([
            'codigo' => [
                'required', 'string', 'max:50',
                Rule::unique('materiales', 'codigo')->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'descripcion' => [
                'required', 'string', 'max:200',
                Rule::unique('materiales', 'descripcion')->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'unidad_id' => ['required', 'integer', 'exists:unidades,id'],
            'costo_estandar' => ['nullable', 'numeric', 'min:0'],
            'activo' => ['nullable', 'in:0,1'],
        ]);

        $codigo = trim((string) $data['codigo']);

        $data['empresa_id'] = $empresaId;
        $data['unidad'] = $this->unidadTextoFromId((int) $data['unidad_id']);
        $data['sku'] = 'E' . $empresaId . '-' . $codigo;
        $data['activo'] = (int) $r->input('activo', 1);
        $data['costo_estandar'] = round((float) ($data['costo_estandar'] ?? 0), 2);

        if (Material::where('sku', $data['sku'])->exists()) {
            $data['sku'] .= '-' . strtoupper(substr(uniqid(), -4));
        }

        Material::create($data);

        return redirect()
            ->route('inventario.materiales')
            ->with('ok', 'Material creado correctamente.');
    }

    public function edit(Material $material)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $material->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este material.');
        }

        $unidades = $this->unidadesList();

        return view('inventario.materiales.edit', compact('material', 'unidades'));
    }

    public function update(Request $r, Material $material)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $material->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este material.');
        }

        $data = $r->validate([
            'codigo' => [
                'required', 'string', 'max:50',
                Rule::unique('materiales', 'codigo')
                    ->ignore($material->id)
                    ->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'descripcion' => [
                'required', 'string', 'max:200',
                Rule::unique('materiales', 'descripcion')
                    ->ignore($material->id)
                    ->where(fn ($q) => $q->where('empresa_id', $empresaId)),
            ],
            'unidad_id' => ['required', 'integer', 'exists:unidades,id'],
            'costo_estandar' => ['nullable', 'numeric', 'min:0'],
            'activo' => ['required', 'in:0,1'],
        ]);

        $codigo = trim((string) $data['codigo']);
        $nuevoSku = 'E' . $empresaId . '-' . $codigo;

        if (Material::where('sku', $nuevoSku)->where('id', '!=', $material->id)->exists()) {
            $nuevoSku .= '-' . strtoupper(substr(uniqid(), -4));
        }

        $data['unidad'] = $this->unidadTextoFromId((int) $data['unidad_id']);
        $data['sku'] = $nuevoSku;
        $data['activo'] = (int) $r->input('activo', 0);
        $data['costo_estandar'] = round((float) ($data['costo_estandar'] ?? 0), 2);

        $material->update($data);

        return redirect()
            ->route('inventario.materiales')
            ->with('ok', 'Material actualizado correctamente.');
    }

    public function destroy(Material $material)
    {
        $empresaId = $this->empresaIdOrAbort();

        if ((int) $material->empresa_id !== $empresaId) {
            abort(403, 'No tienes acceso a este material.');
        }

        $material->delete();

        return redirect()
            ->route('inventario.materiales')
            ->with('ok', 'Material eliminado correctamente.');
    }
}
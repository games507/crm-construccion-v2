<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\Proveedor;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    private function empresaIdActual(): ?int
    {
        $empresaId = EmpresaScope::getId();

        if (!$empresaId || (int) $empresaId === 0) {
            $empresaId = auth()->user()->empresa_id ?? null;
        }

        if (!$empresaId || (int) $empresaId === 0) {
            $empresaId = Empresa::query()->orderBy('id')->value('id');
        }

        return $empresaId ? (int) $empresaId : null;
    }

    public function index(Request $request)
    {
        $empresaId = $this->empresaIdActual();

        if (!$empresaId) {
            return back()->withErrors([
                'empresa' => 'No existe ninguna empresa registrada en el sistema.',
            ]);
        }

        $q = trim($request->q ?? '');

        $proveedores = Proveedor::query()
            ->where('empresa_id', $empresaId)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nombre', 'like', "%{$q}%")
                        ->orWhere('codigo', 'like', "%{$q}%")
                        ->orWhere('ruc', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.proveedores.index', compact('proveedores', 'q'));
    }

    public function store(Request $request)
    {
        $empresaId = $this->empresaIdActual();

        if (!$empresaId) {
            return back()->withErrors([
                'empresa' => 'No existe ninguna empresa registrada en el sistema.',
            ])->withInput();
        }

        $data = $request->validate([
            'codigo' => ['nullable', 'max:30'],
            'nombre' => ['required', 'max:160'],
            'ruc' => ['nullable', 'max:50'],
            'dv' => ['nullable', 'max:10'],
            'telefono' => ['nullable', 'max:50'],
            'email' => ['nullable', 'email', 'max:120'],
            'contacto' => ['nullable', 'max:120'],
            'direccion' => ['nullable', 'max:255'],
        ]);

        $data['empresa_id'] = $empresaId;
        $data['activo'] = true;

        Proveedor::create($data);

        return redirect()
            ->route('admin.proveedores.index')
            ->with('ok', 'Proveedor creado correctamente.');
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $data = $request->validate([
            'codigo' => ['nullable', 'max:30'],
            'nombre' => ['required', 'max:160'],
            'ruc' => ['nullable', 'max:50'],
            'dv' => ['nullable', 'max:10'],
            'telefono' => ['nullable', 'max:50'],
            'email' => ['nullable', 'email', 'max:120'],
            'contacto' => ['nullable', 'max:120'],
            'direccion' => ['nullable', 'max:255'],
        ]);

        $proveedore->update($data);

        return redirect()
            ->route('admin.proveedores.index')
            ->with('ok', 'Proveedor actualizado correctamente.');
    }

    public function toggle(Proveedor $proveedore)
    {
        $proveedore->activo = !$proveedore->activo;
        $proveedore->save();

        return redirect()
            ->route('admin.proveedores.index')
            ->with('ok', 'Estado actualizado correctamente.');
    }
}
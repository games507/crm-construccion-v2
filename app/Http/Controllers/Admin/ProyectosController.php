<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Empresa;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;

class ProyectosController extends Controller
{
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        $scopeEmpresaId = (int) EmpresaScope::getId();     // super admin elige empresa
        $userEmpresaId  = (int) ($user->empresa_id ?? 0);  // usuario normal

        $empresaId = $scopeEmpresaId ?: $userEmpresaId;

        abort_if($empresaId <= 0, 403, 'No hay empresa seleccionada o asociada al usuario.');
        return $empresaId;
    }

    public function index()
    {
        $empresaId = $this->empresaIdOrAbort();

        $q      = trim((string) request('q', ''));
        $estado = trim((string) request('estado', ''));

        $proyectos = Proyecto::where('empresa_id', $empresaId)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('codigo', 'like', "%{$q}%")
                       ->orWhere('nombre', 'like', "%{$q}%")
                       ->orWhere('ubicacion', 'like', "%{$q}%");
                });
            })
            ->when($estado !== '', function ($query) use ($estado) {
                $query->where('estado', $estado);
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        $empresaId = $this->empresaIdOrAbort();

        if (auth()->user()->can('empresas.ver')) {
            $empresas = Empresa::orderBy('nombre')->get();
        } else {
            $empresas = Empresa::where('id', $empresaId)->get();
        }

        return view('admin.proyectos.create', compact('empresas', 'empresaId'));
    }

    public function store(Request $request)
    {
        $empresaId = $this->empresaIdOrAbort();

        $empresaToSave = auth()->user()->can('empresas.ver')
            ? (int) $request->input('empresa_id')
            : $empresaId;

        $data = $request->validate([
            'codigo'       => ['nullable', 'string', 'max:40'],
            'nombre'       => ['required', 'string', 'max:160'],
            'ubicacion'    => ['nullable', 'string', 'max:220'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date'],
            'estado'       => ['required', 'string', 'max:30'],
            'presupuesto'  => ['nullable', 'numeric', 'min:0'],
        ]);

        // defaults
        $data['empresa_id']  = $empresaToSave;
        $data['presupuesto'] = (float) ($data['presupuesto'] ?? 0);
        $data['activo']      = $request->has('activo') ? 1 : 0;

        if (!empty($data['fecha_inicio']) && !empty($data['fecha_fin'])) {
            if (strtotime($data['fecha_fin']) < strtotime($data['fecha_inicio'])) {
                return back()
                    ->withErrors(['fecha_fin' => 'La fecha fin no puede ser menor que la fecha inicio.'])
                    ->withInput();
            }
        }

        Proyecto::create($data);

        return redirect()
            ->route('admin.proyectos')
            ->with('ok', '✅ Proyecto creado correctamente.');
    }

    public function edit($id)
    {
        $empresaId = $this->empresaIdOrAbort();

        $proyecto = Proyecto::where('empresa_id', $empresaId)->findOrFail($id);

        if (auth()->user()->can('empresas.ver')) {
            $empresas = Empresa::orderBy('nombre')->get();
        } else {
            $empresas = Empresa::where('id', $empresaId)->get();
        }

        return view('admin.proyectos.edit', compact('proyecto', 'empresas', 'empresaId'));
    }

    public function update(Request $request, $id)
    {
        $empresaId = $this->empresaIdOrAbort();

        $proyecto = Proyecto::where('empresa_id', $empresaId)->findOrFail($id);

        $empresaToSave = auth()->user()->can('empresas.ver')
            ? (int) $request->input('empresa_id')
            : $empresaId;

        $data = $request->validate([
            'codigo'       => ['nullable', 'string', 'max:40'],
            'nombre'       => ['required', 'string', 'max:160'],
            'ubicacion'    => ['nullable', 'string', 'max:220'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date'],
            'estado'       => ['required', 'string', 'max:30'],
            'presupuesto'  => ['nullable', 'numeric', 'min:0'],
        ]);

        $data['empresa_id']  = $empresaToSave;
        $data['presupuesto'] = (float) ($data['presupuesto'] ?? 0);
        $data['activo']      = $request->has('activo') ? 1 : 0;

        if (!empty($data['fecha_inicio']) && !empty($data['fecha_fin'])) {
            if (strtotime($data['fecha_fin']) < strtotime($data['fecha_inicio'])) {
                return back()
                    ->withErrors(['fecha_fin' => 'La fecha fin no puede ser menor que la fecha inicio.'])
                    ->withInput();
            }
        }

        $proyecto->update($data);

        return redirect()
            ->route('admin.proyectos.edit', $proyecto->id)
            ->with('ok', '✅ Proyecto actualizado correctamente.');
    }
}

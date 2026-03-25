<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\Empresa;
use App\Models\User;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class ProyectosController extends Controller
{
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        $scopeEmpresaId = (int) EmpresaScope::getId();
        $userEmpresaId  = (int) ($user->empresa_id ?? 0);

        $empresaId = $scopeEmpresaId ?: $userEmpresaId;

        abort_if($empresaId <= 0, 403, 'No hay empresa seleccionada o asociada al usuario.');

        return $empresaId;
    }

    /**
     * Detecta el campo de nombre en users
     */
    private function userNameField(): string
    {
        if (Schema::hasColumn('users', 'name')) return 'name';
        if (Schema::hasColumn('users', 'nombre')) return 'nombre';
        if (Schema::hasColumn('users', 'nombre_completo')) return 'nombre_completo';

        return 'id'; // fallback
    }

    public function index()
    {
        $empresaId = $this->empresaIdOrAbort();

        $q      = trim((string) request('q', ''));
        $estado = trim((string) request('estado', ''));

        $proyectos = Proyecto::with(['responsable'])
            ->where('empresa_id', $empresaId)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('codigo', 'like', "%{$q}%")
                        ->orWhere('nombre', 'like', "%{$q}%")
                        ->orWhere('ubicacion', 'like', "%{$q}%")
                        ->orWhere('descripcion', 'like', "%{$q}%");
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
        $nameField = $this->userNameField();

        if (auth()->user()->can('empresas.ver')) {
            $empresas = Empresa::orderBy('nombre')->get();
        } else {
            $empresas = Empresa::where('id', $empresaId)->get();
        }

        $usuarios = User::query()
            ->where('empresa_id', $empresaId)
            ->orderBy($nameField)
            ->get();

        return view('admin.proyectos.create', compact('empresas', 'empresaId', 'usuarios', 'nameField'));
    }

    public function store(Request $request)
    {
        $empresaId = $this->empresaIdOrAbort();

        $empresaToSave = auth()->user()->can('empresas.ver')
            ? (int) $request->input('empresa_id')
            : $empresaId;

        $data = $request->validate([
            'codigo'         => ['nullable', 'string', 'max:40'],
            'nombre'         => ['required', 'string', 'max:160'],
            'descripcion'    => ['nullable', 'string'],
            'ubicacion'      => ['nullable', 'string', 'max:220'],
            'responsable_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'fecha_inicio'   => ['nullable', 'date'],
            'fecha_fin'      => ['nullable', 'date'],
            'estado'         => ['required', Rule::in([
                Proyecto::ESTADO_PLANEADO,
                Proyecto::ESTADO_EJECUCION,
                Proyecto::ESTADO_PAUSADO,
                Proyecto::ESTADO_FINALIZADO,
            ])],
            'presupuesto'    => ['nullable', 'numeric', 'min:0'],
        ]);

        $data['responsable_id'] = $this->usuarioPerteneceEmpresa($request->input('responsable_id'), $empresaToSave)
            ? $request->input('responsable_id')
            : null;

        $data['empresa_id']  = $empresaToSave;
        $data['presupuesto'] = (float) ($data['presupuesto'] ?? 0);
        $data['activo']      = $request->boolean('activo');

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
public function show($id)
{
    $empresaId = $this->empresaIdOrAbort();

    $proyecto = Proyecto::with([
            'responsable',
            'fases',
            'tareas.responsable',
            'tareas.fase'
        ])
        ->where('empresa_id', $empresaId)
        ->findOrFail($id);

    $nameField = $this->userNameField();

    $usuarios = \App\Models\User::query()
        ->where('empresa_id', $empresaId)
        ->orderBy($nameField)
        ->get();

    $stats = [
        'tareas_total'       => $proyecto->tareas->count(),
        'tareas_pendientes'  => $proyecto->tareas->where('estado', 'pendiente')->count(),
        'tareas_proceso'     => $proyecto->tareas->where('estado', 'en_proceso')->count(),
        'tareas_finalizadas' => $proyecto->tareas->where('estado', 'finalizada')->count(),
        'tareas_pausadas'    => $proyecto->tareas->where('estado', 'pausada')->count(),
        'fases_total'        => $proyecto->fases->count(),
        'fases_completadas'  => $proyecto->fases->filter(fn($f) => (float)$f->porcentaje >= 100)->count(),
    ];

    return view('admin.proyectos.show', compact('proyecto', 'usuarios', 'nameField', 'stats'));
}
    public function edit($id)
    {
        $empresaId = $this->empresaIdOrAbort();
        $nameField = $this->userNameField();

        $proyecto = Proyecto::where('empresa_id', $empresaId)->findOrFail($id);

        if (auth()->user()->can('empresas.ver')) {
            $empresas = Empresa::orderBy('nombre')->get();
        } else {
            $empresas = Empresa::where('id', $empresaId)->get();
        }

        $usuarios = User::query()
            ->where('empresa_id', $proyecto->empresa_id)
            ->orderBy($nameField)
            ->get();

        return view('admin.proyectos.edit', compact('proyecto', 'empresas', 'empresaId', 'usuarios', 'nameField'));
    }

    public function update(Request $request, $id)
    {
        $empresaId = $this->empresaIdOrAbort();

        $proyecto = Proyecto::where('empresa_id', $empresaId)->findOrFail($id);

        $empresaToSave = auth()->user()->can('empresas.ver')
            ? (int) $request->input('empresa_id')
            : $empresaId;

        $data = $request->validate([
            'codigo'         => ['nullable', 'string', 'max:40'],
            'nombre'         => ['required', 'string', 'max:160'],
            'descripcion'    => ['nullable', 'string'],
            'ubicacion'      => ['nullable', 'string', 'max:220'],
            'responsable_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'fecha_inicio'   => ['nullable', 'date'],
            'fecha_fin'      => ['nullable', 'date'],
            'estado'         => ['required', Rule::in([
                Proyecto::ESTADO_PLANEADO,
                Proyecto::ESTADO_EJECUCION,
                Proyecto::ESTADO_PAUSADO,
                Proyecto::ESTADO_FINALIZADO,
            ])],
            'presupuesto'    => ['nullable', 'numeric', 'min:0'],
        ]);

        $data['responsable_id'] = $this->usuarioPerteneceEmpresa($request->input('responsable_id'), $empresaToSave)
            ? $request->input('responsable_id')
            : null;

        $data['empresa_id']  = $empresaToSave;
        $data['presupuesto'] = (float) ($data['presupuesto'] ?? 0);
        $data['activo']      = $request->boolean('activo');

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

    private function usuarioPerteneceEmpresa($userId, int $empresaId): bool
    {
        if (empty($userId)) {
            return false;
        }

        return User::where('id', $userId)
            ->where('empresa_id', $empresaId)
            ->exists();
    }
}
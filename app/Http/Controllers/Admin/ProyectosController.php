<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\User;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;

class ProyectosController extends Controller
{
    /**
     * Obtener empresa activa
     */
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        $isSuperAdmin = method_exists($user, 'hasRole')
            ? $user->hasRole('SuperAdmin')
            : false;

        $empresaId = $isSuperAdmin
            ? EmpresaScope::getId()
            : ($user->empresa_id ?? null);

        abort_if(!$empresaId, 403, 'No hay empresa seleccionada.');

        return (int) $empresaId;
    }

    /**
     * Campo nombre dinámico
     */
    private function userNameField(): string
    {
        return 'name';
    }

    /**
     * LISTADO
     */
    public function index()
    {
        $empresaId = $this->empresaIdOrAbort();

        $proyectos = Proyecto::with('responsable')
            ->where('empresa_id', $empresaId)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.proyectos.index', compact('proyectos'));
    }

    /**
     * CREAR
     */
    public function create()
    {
        $empresaId = $this->empresaIdOrAbort();
        $nameField = $this->userNameField();

        $usuarios = User::where('empresa_id', $empresaId)
            ->orderBy($nameField)
            ->get();

        return view('admin.proyectos.create', compact('usuarios', 'nameField'));
    }

    /**
     * GUARDAR
     */
    public function store(Request $request)
    {
        $empresaId = $this->empresaIdOrAbort();

        $data = $request->validate([
            'codigo'         => 'nullable|string|max:50',
            'nombre'         => 'required|string|max:150',
            'descripcion'    => 'nullable|string',
            'ubicacion'      => 'nullable|string',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date',
            'responsable_id' => 'nullable|exists:users,id',
            'presupuesto'    => 'nullable|numeric',
        ]);

        $data['empresa_id'] = $empresaId;
        $data['estado'] = Proyecto::ESTADO_PLANEADO;
        $data['activo'] = 1;

        Proyecto::create($data);

        return redirect()
            ->route('admin.proyectos')
            ->with('ok', 'Proyecto creado correctamente');
    }

    /**
     * EDITAR
     */
    public function edit($id)
    {
        $empresaId = $this->empresaIdOrAbort();
        $nameField = $this->userNameField();

        $proyecto = Proyecto::where('empresa_id', $empresaId)
            ->findOrFail($id);

        $usuarios = User::where('empresa_id', $empresaId)
            ->orderBy($nameField)
            ->get();

        return view('admin.proyectos.edit', compact('proyecto', 'usuarios', 'nameField'));
    }

    /**
     * ACTUALIZAR
     */
    public function update(Request $request, $id)
    {
        $empresaId = $this->empresaIdOrAbort();

        $proyecto = Proyecto::where('empresa_id', $empresaId)
            ->findOrFail($id);

        $data = $request->validate([
            'codigo'         => 'nullable|string|max:50',
            'nombre'         => 'required|string|max:150',
            'descripcion'    => 'nullable|string',
            'ubicacion'      => 'nullable|string',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date',
            'estado'         => 'required|string',
            'responsable_id' => 'nullable|exists:users,id',
            'presupuesto'    => 'nullable|numeric',
        ]);

        $proyecto->update($data);

        return redirect()
            ->route('admin.proyectos.show', $proyecto->id)
            ->with('ok', 'Proyecto actualizado');
    }

    /**
     * DETALLE (SHOW)
     */
    public function show($id)
    {
        $empresaId = $this->empresaIdOrAbort();

        $proyecto = Proyecto::with([
                'responsable',
                'fases',
                'tareas.responsable',
                'tareas.fase',
                'costos',
                'cuentasPorPagar',
            ])
            ->where('empresa_id', $empresaId)
            ->findOrFail($id);

        $nameField = $this->userNameField();

        $usuarios = User::where('empresa_id', $empresaId)
            ->orderBy($nameField)
            ->get();

        // =========================
        // STATS
        // =========================
        $stats = [
            'tareas_total'       => $proyecto->tareas->count(),
            'tareas_pendientes'  => $proyecto->tareas->where('estado', 'pendiente')->count(),
            'tareas_proceso'     => $proyecto->tareas->where('estado', 'en_proceso')->count(),
            'tareas_finalizadas' => $proyecto->tareas->where('estado', 'finalizada')->count(),
            'tareas_pausadas'    => $proyecto->tareas->where('estado', 'pausada')->count(),
            'fases_total'        => $proyecto->fases->count(),
            'fases_completadas'  => $proyecto->fases->filter(fn($f) => (float)$f->porcentaje >= 100)->count(),
        ];

        // =========================
        // COSTOS
        // =========================
        $costos = $proyecto->costos()
            ->latest('fecha')
            ->latest('id')
            ->get();

        // =========================
        // FINANZAS
        // =========================
        $presupuesto = (float) ($proyecto->presupuesto ?? 0);
        $ejecutado   = (float) $costos->sum('monto');

        $finanzas = [
            'presupuesto'          => round($presupuesto, 2),
            'ejecutado'            => round($ejecutado, 2),
            'saldo_disponible'     => round($presupuesto - $ejecutado, 2),
            'porcentaje_consumido' => $presupuesto > 0
                ? round(($ejecutado / $presupuesto) * 100, 2)
                : 0,
        ];

        return view('admin.proyectos.show', compact(
            'proyecto',
            'usuarios',
            'nameField',
            'stats',
            'costos',
            'finanzas'
        ));
    }
}
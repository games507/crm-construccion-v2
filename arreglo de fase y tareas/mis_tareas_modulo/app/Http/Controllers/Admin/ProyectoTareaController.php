<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\ProyectoTarea;
use App\Models\User;
use App\Notifications\TareaAsignadaNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProyectoTareaController extends Controller
{
    private function userNameField(): string
    {
        if (Schema::hasColumn('users', 'name')) return 'name';
        if (Schema::hasColumn('users', 'nombre')) return 'nombre';
        if (Schema::hasColumn('users', 'nombre_completo')) return 'nombre_completo';

        return 'id';
    }

    private function recalcularProgresoProyecto(int $proyectoId): void
    {
        $proyecto = Proyecto::with('fases.tareas')->find($proyectoId);

        if (!$proyecto) {
            return;
        }

        foreach ($proyecto->fases as $fase) {
            $promedioFase = $fase->tareas()->avg('porcentaje');
            $fase->porcentaje = $promedioFase ?? 0;
            $fase->save();
        }

        $promedioProyecto = $proyecto->fases()->avg('porcentaje');
        $proyecto->porcentaje = $promedioProyecto ?? 0;

        if ((float) $proyecto->porcentaje >= 100) {
            $proyecto->estado = 'finalizado';
        } elseif ((float) $proyecto->porcentaje > 0) {
            $proyecto->estado = 'en_ejecucion';
        } else {
            $proyecto->estado = 'planeado';
        }

        $proyecto->save();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proyecto_id'    => ['required', 'exists:proyectos,id'],
            'fase_id'        => ['nullable', 'exists:proyecto_fases,id'],
            'responsable_id' => ['nullable', 'exists:users,id'],
            'nombre'         => ['required', 'string', 'max:180'],
            'descripcion'    => ['nullable', 'string'],
            'estado'         => ['required', 'in:pendiente,en_proceso,finalizada,pausada'],
            'fecha_inicio'   => ['nullable', 'date'],
            'fecha_fin'      => ['nullable', 'date'],
            'porcentaje'     => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['porcentaje'] = isset($data['porcentaje']) && $data['porcentaje'] !== ''
            ? (float) $data['porcentaje']
            : 0;

        if ($data['estado'] === 'finalizada') {
            $data['porcentaje'] = 100;
        }

        if (!empty($data['fecha_inicio']) && !empty($data['fecha_fin'])) {
            if (strtotime($data['fecha_fin']) < strtotime($data['fecha_inicio'])) {
                return back()
                    ->withErrors(['fecha_fin' => 'La fecha fin no puede ser menor que la fecha inicio.'])
                    ->withInput();
            }
        }

        $tarea = ProyectoTarea::create($data);

        $this->recalcularProgresoProyecto((int) $tarea->proyecto_id);

        if (!empty($tarea->responsable_id)) {
            $user = User::find($tarea->responsable_id);

            if ($user) {
                $user->notify(new TareaAsignadaNotification($tarea));
            }
        }

        return back()->with('ok', 'Tarea agregada correctamente.');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'id'         => ['required', 'exists:proyecto_tareas,id'],
            'porcentaje' => ['required', 'numeric', 'min:0', 'max:100'],
            'estado'     => ['required', 'in:pendiente,en_proceso,finalizada,pausada'],
        ]);

        $tarea = ProyectoTarea::findOrFail($data['id']);

        $porcentaje = (float) $data['porcentaje'];

        if ($data['estado'] === 'finalizada') {
            $porcentaje = 100;
        }

        $tarea->update([
            'porcentaje' => $porcentaje,
            'estado'     => $data['estado'],
        ]);

        $this->recalcularProgresoProyecto((int) $tarea->proyecto_id);

        return back()->with('ok', 'Tarea actualizada correctamente.');
    }

    public function edit($id)
    {
        $tarea = ProyectoTarea::with(['proyecto.fases'])->findOrFail($id);

        $nameField = $this->userNameField();

        $usuarios = User::query()
            ->when(
                Schema::hasColumn('users', 'empresa_id') && !empty($tarea->proyecto->empresa_id),
                fn ($q) => $q->where('empresa_id', $tarea->proyecto->empresa_id)
            )
            ->orderBy($nameField)
            ->get();

        $fases = $tarea->proyecto->fases()->orderBy('orden')->get();

        return view('admin.proyectos.tareas.edit', compact('tarea', 'usuarios', 'fases', 'nameField'));
    }

    public function updateFull(Request $request, $id)
    {
        $tarea = ProyectoTarea::findOrFail($id);

        $responsableAnterior = $tarea->responsable_id;
        $proyectoAnterior = $tarea->proyecto_id;

        $data = $request->validate([
            'fase_id'        => ['nullable', 'exists:proyecto_fases,id'],
            'responsable_id' => ['nullable', 'exists:users,id'],
            'nombre'         => ['required', 'string', 'max:180'],
            'descripcion'    => ['nullable', 'string'],
            'estado'         => ['required', 'in:pendiente,en_proceso,finalizada,pausada'],
            'fecha_inicio'   => ['nullable', 'date'],
            'fecha_fin'      => ['nullable', 'date'],
            'porcentaje'     => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['porcentaje'] = isset($data['porcentaje']) && $data['porcentaje'] !== ''
            ? (float) $data['porcentaje']
            : 0;

        if ($data['estado'] === 'finalizada') {
            $data['porcentaje'] = 100;
        }

        if (!empty($data['fecha_inicio']) && !empty($data['fecha_fin'])) {
            if (strtotime($data['fecha_fin']) < strtotime($data['fecha_inicio'])) {
                return back()
                    ->withErrors(['fecha_fin' => 'La fecha fin no puede ser menor que la fecha inicio.'])
                    ->withInput();
            }
        }

        $tarea->update($data);

        $this->recalcularProgresoProyecto((int) $tarea->proyecto_id);

        if ((int) $proyectoAnterior !== (int) $tarea->proyecto_id) {
            $this->recalcularProgresoProyecto((int) $proyectoAnterior);
        }

        if (!empty($data['responsable_id']) && (int) $data['responsable_id'] !== (int) $responsableAnterior) {
            $user = User::find($data['responsable_id']);

            if ($user) {
                $user->notify(new TareaAsignadaNotification($tarea->fresh()));
            }
        }

        return redirect()
            ->route('admin.proyectos.show', $tarea->proyecto_id)
            ->with('ok', 'Tarea editada correctamente.');
    }

    public function misTareas(Request $request)
    {
        $user = auth()->user();

        abort_if(!$user, 403);

        $q = trim((string) $request->get('q', ''));
        $estado = trim((string) $request->get('estado', ''));
        $proyectoId = trim((string) $request->get('proyecto_id', ''));
        $vencidas = $request->boolean('vencidas');

        $tareasQuery = ProyectoTarea::query()
            ->with(['proyecto', 'fase', 'responsable'])
            ->where('responsable_id', $user->id);

        if ($q !== '') {
            $tareasQuery->where(function ($qq) use ($q) {
                $qq->where('nombre', 'like', "%{$q}%")
                    ->orWhere('descripcion', 'like', "%{$q}%")
                    ->orWhereHas('proyecto', function ($p) use ($q) {
                        $p->where('nombre', 'like', "%{$q}%");
                    })
                    ->orWhereHas('fase', function ($f) use ($q) {
                        $f->where('nombre', 'like', "%{$q}%");
                    });
            });
        }

        if ($estado !== '') {
            $tareasQuery->where('estado', $estado);
        }

        if ($proyectoId !== '') {
            $tareasQuery->where('proyecto_id', (int) $proyectoId);
        }

        if ($vencidas) {
            $tareasQuery->whereNotNull('fecha_fin')
                ->whereDate('fecha_fin', '<', now()->toDateString())
                ->where('estado', '!=', ProyectoTarea::ESTADO_FINALIZADA);
        }

        $tareas = $tareasQuery
            ->orderByRaw("CASE WHEN fecha_fin IS NULL THEN 1 ELSE 0 END")
            ->orderBy('fecha_fin')
            ->latest('id')
            ->get();

        $todasMisTareas = ProyectoTarea::query()
            ->where('responsable_id', $user->id)
            ->get();

        $stats = [
            'total' => $todasMisTareas->count(),
            'pendientes' => $todasMisTareas->where('estado', ProyectoTarea::ESTADO_PENDIENTE)->count(),
            'en_proceso' => $todasMisTareas->where('estado', ProyectoTarea::ESTADO_EN_PROCESO)->count(),
            'finalizadas' => $todasMisTareas->where('estado', ProyectoTarea::ESTADO_FINALIZADA)->count(),
            'vencidas' => $todasMisTareas->filter(function ($t) {
                return $t->fecha_fin
                    && $t->fecha_fin->lt(now()->startOfDay())
                    && $t->estado !== ProyectoTarea::ESTADO_FINALIZADA;
            })->count(),
        ];

        $proyectos = Proyecto::query()
            ->whereIn('id', $todasMisTareas->pluck('proyecto_id')->filter()->unique()->values())
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return view('admin.proyectos.tareas.mis_tareas', compact(
            'tareas',
            'stats',
            'proyectos',
            'q',
            'estado',
            'proyectoId',
            'vencidas'
        ));
    }

    public function updateMisTarea(Request $request, $id)
    {
        $user = auth()->user();

        abort_if(!$user, 403);

        $tarea = ProyectoTarea::where('responsable_id', $user->id)->findOrFail($id);

        $data = $request->validate([
            'estado'     => ['required', 'in:pendiente,en_proceso,finalizada,pausada'],
            'porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $porcentaje = isset($data['porcentaje']) && $data['porcentaje'] !== ''
            ? (float) $data['porcentaje']
            : (float) $tarea->porcentaje;

        if ($data['estado'] === ProyectoTarea::ESTADO_FINALIZADA) {
            $porcentaje = 100;
        }

        $tarea->update([
            'estado' => $data['estado'],
            'porcentaje' => $porcentaje,
        ]);

        $this->recalcularProgresoProyecto((int) $tarea->proyecto_id);

        return back()->with('ok', 'Tarea actualizada correctamente.');
    }

    public function destroy($id)
    {
        $tarea = ProyectoTarea::findOrFail($id);
        $proyectoId = $tarea->proyecto_id;

        $tarea->delete();

        $this->recalcularProgresoProyecto((int) $proyectoId);

        return redirect()
            ->route('admin.proyectos.show', $proyectoId)
            ->with('ok', 'Tarea eliminada correctamente.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use App\Models\ProyectoFase;
use App\Models\ProyectoTarea;
use App\Models\User;
use App\Notifications\TareaAsignadaNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProyectoTareaController extends Controller
{
    private function userNameField(): string
    {
        if (Schema::hasColumn('users', 'name')) {
            return 'name';
        }

        if (Schema::hasColumn('users', 'nombre')) {
            return 'nombre';
        }

        if (Schema::hasColumn('users', 'nombre_completo')) {
            return 'nombre_completo';
        }

        return 'id';
    }

    /**
     * Recalcula el porcentaje de una fase y del proyecto completo
     */
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
            $proyecto->estado = 'en_proceso';
        } else {
            $proyecto->estado = 'pendiente';
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

        // Recalcular progreso del proyecto/fases
        $this->recalcularProgresoProyecto((int) $tarea->proyecto_id);

        // 🔔 Notificación al responsable asignado
        if (!empty($tarea->responsable_id)) {
            $user = User::find($tarea->responsable_id);

            if ($user) {
                $user->notify(new TareaAsignadaNotification($tarea));
            }
        }

        return back()->with('ok', 'Tarea agregada correctamente.');
    }

    /**
     * Actualización rápida desde el detalle del proyecto
     * Solo porcentaje + estado
     */
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

        // Recalcular progreso del proyecto/fases
        $this->recalcularProgresoProyecto((int) $tarea->proyecto_id);

        return back()->with('ok', 'Tarea actualizada correctamente.');
    }

    /**
     * Formulario de edición completa
     */
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

    /**
     * Guardado de edición completa
     */
    public function updateFull(Request $request, $id)
    {
        $tarea = ProyectoTarea::with('proyecto')->findOrFail($id);

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

        $responsableAnterior = $tarea->responsable_id;
        $proyectoAnterior = $tarea->proyecto_id;

        $tarea->update($data);

        // Recalcular proyecto actual
        $this->recalcularProgresoProyecto((int) $tarea->proyecto_id);

        // Si por alguna razón cambió de proyecto, recalcular el anterior también
        if ((int) $proyectoAnterior !== (int) $tarea->proyecto_id) {
            $this->recalcularProgresoProyecto((int) $proyectoAnterior);
        }

        // 🔔 Notificar si cambió el responsable
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

    public function destroy($id)
    {
        $tarea = ProyectoTarea::findOrFail($id);
        $proyectoId = $tarea->proyecto_id;

        $tarea->delete();

        // Recalcular progreso del proyecto/fases
        $this->recalcularProgresoProyecto((int) $proyectoId);

        return redirect()
            ->route('admin.proyectos.show', $proyectoId)
            ->with('ok', 'Tarea eliminada correctamente.');
    }
}
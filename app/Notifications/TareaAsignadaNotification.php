<?php

namespace App\Notifications;

use App\Models\ProyectoTarea;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TareaAsignadaNotification extends Notification
{
    use Queueable;

    public function __construct(public ProyectoTarea $tarea)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo'        => 'tarea_asignada',
            'titulo'      => 'Nueva tarea asignada',
            'mensaje'     => 'Se te asignó la tarea: ' . $this->tarea->nombre,
            'tarea_id'    => $this->tarea->id,
            'proyecto_id' => $this->tarea->proyecto_id,
            'estado'      => $this->tarea->estado,
            'url'         => route('admin.proyectos.tareas.edit', $this->tarea->id),
        ];
    }
}
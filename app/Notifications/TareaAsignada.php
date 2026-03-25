<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TareaAsignada extends Notification
{
    use Queueable;

    public $tarea;

    public function __construct($tarea)
    {
        $this->tarea = $tarea;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'titulo' => 'Nueva tarea asignada',
            'mensaje' => 'Se te asignó la tarea: '.$this->tarea->nombre,
            'tarea_id' => $this->tarea->id,
        ];
    }
}
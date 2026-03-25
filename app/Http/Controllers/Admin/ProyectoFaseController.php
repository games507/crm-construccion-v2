<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProyectoFase;
use Illuminate\Http\Request;

class ProyectoFaseController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'proyecto_id' => ['required', 'exists:proyectos,id'],
            'nombre'      => ['required', 'string', 'max:150'],
            'orden'       => ['nullable', 'integer'],
            'porcentaje'  => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['porcentaje'] = isset($data['porcentaje']) && $data['porcentaje'] !== ''
            ? (float) $data['porcentaje']
            : 0;

        ProyectoFase::create($data);

        return back()->with('ok', 'Fase agregada correctamente');
    }
}
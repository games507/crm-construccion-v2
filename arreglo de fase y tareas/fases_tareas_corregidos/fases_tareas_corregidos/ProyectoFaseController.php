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
            'nombre'     => ['required', 'string', 'max:150'],
            'orden'      => ['nullable', 'integer', 'min:0'],
            'porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['porcentaje'] = isset($data['porcentaje']) && $data['porcentaje'] !== ''
            ? (float) $data['porcentaje']
            : 0;

        ProyectoFase::create($data);

        return back()->with('ok', '✅ Fase agregada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $fase = ProyectoFase::findOrFail((int) $id);

        $data = $request->validate([
            'nombre'     => ['required', 'string', 'max:150'],
            'orden'      => ['nullable', 'integer', 'min:0'],
            'porcentaje' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $data['orden'] = (int) ($data['orden'] ?? 0);
        $data['porcentaje'] = isset($data['porcentaje']) && $data['porcentaje'] !== ''
            ? (float) $data['porcentaje']
            : 0;

        $fase->update($data);

        return back()->with('ok', '✅ Fase actualizada correctamente.');
    }

    public function destroy($id)
    {
        $fase = ProyectoFase::with('tareas')->findOrFail((int) $id);

        // No eliminamos tareas; solo las dejamos sin fase para no perder información.
        $fase->tareas()->update(['fase_id' => null]);

        $fase->delete();

        return back()->with('ok', '✅ Fase eliminada correctamente. Las tareas quedaron sin fase.');
    }
}

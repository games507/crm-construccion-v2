<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvExistencia;
use App\Models\Almacen;

class InventarioController extends Controller
{
    public function existencias(Request $r)
    {
        $empresaId = (int) auth()->user()->empresa_id;

        if ($empresaId <= 0) {
            abort(403, 'Tu usuario no tiene empresa asignada.');
        }

        $almacenId = (int) $r->get('almacen_id', 0);
        $q = trim((string) $r->get('q', ''));

        $query = InvExistencia::query()
            ->with([
                'material:id,codigo,sku,descripcion,unidad,unidad_id',
                'material.unidadRef:id,codigo,descripcion', // ✅ aquí
                'almacen:id,codigo,nombre',
            ])
            ->where('empresa_id', $empresaId);

        if ($almacenId > 0) {
            $query->where('almacen_id', $almacenId);
        }

        if ($q !== '') {
            $query->whereHas('material', function ($mq) use ($q) {
                $mq->where('descripcion', 'like', "%{$q}%")
                   ->orWhere('codigo', 'like', "%{$q}%")
                   ->orWhere('sku', 'like', "%{$q}%");
            });
        }

        $existencias = $query
            ->orderBy('almacen_id')
            ->orderBy('material_id')
            ->paginate(20)
            ->withQueryString();

        $almacenes = Almacen::where('empresa_id', $empresaId)
            ->orderBy('nombre')
            ->get(['id','codigo','nombre']);

        return view('inventario.existencias.index', compact('existencias','almacenes','almacenId','q'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioExistenciasController extends Controller
{
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();
        $empresaId = (int) ($user->empresa_id ?? 0);

        if ($empresaId <= 0) {
            abort(403, 'Tu usuario no tiene empresa asignada.');
        }

        return $empresaId;
    }

    /**
     * API para React (Existencias)
     *
     * Query params:
     * - q: busca por codigo / sku / descripcion
     * - almacen_id: filtra por almacén
     * - per_page: tamaño de página (default 50, max 200)
     */
    public function api(Request $request)
    {
        $empresaId = $this->empresaIdOrAbort();

        $q = trim((string) $request->query('q', ''));
        $almacenId = (int) $request->query('almacen_id', 0);

        $perPage = (int) $request->query('per_page', 50);
        if ($perPage <= 0) $perPage = 50;
        if ($perPage > 200) $perPage = 200;

        $query = DB::table('existencias as e')
            ->where('e.empresa_id', $empresaId)

            ->join('materiales as m', function ($join) use ($empresaId) {
                $join->on('m.id', '=', 'e.material_id')
                     ->where('m.empresa_id', '=', $empresaId);
            })

            ->join('almacenes as a', function ($join) use ($empresaId) {
                $join->on('a.id', '=', 'e.almacen_id')
                     ->where('a.empresa_id', '=', $empresaId);
            })

            ->when($almacenId > 0, function ($qq) use ($almacenId) {
                $qq->where('e.almacen_id', $almacenId);
            })

            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('m.codigo', 'like', "%{$q}%")
                      ->orWhere('m.sku', 'like', "%{$q}%")
                      ->orWhere('m.descripcion', 'like', "%{$q}%");
                });
            })

            ->select([
                'e.id',
                'm.id as material_id',
                'a.id as almacen_id',

                // En tu BD no hay "nombre", usamos el codigo como material principal
                DB::raw('COALESCE(m.codigo, "") as material'),
                DB::raw('COALESCE(m.codigo, "") as codigo'),
                DB::raw('COALESCE(m.sku, "") as sku'),

                // no existe categoria -> devolvemos vacío
                DB::raw('"" as categoria'),

                // almacén
                DB::raw('COALESCE(a.codigo, "") as almacen_codigo'),
                DB::raw('COALESCE(a.nombre, "") as almacen'),

                // existencia: usamos stock (tu columna real)
                DB::raw('COALESCE(e.stock, 0) as existencia'),

                // mínimo no existe en tu tabla -> 0
                DB::raw('0 as minimo'),

                // extras útiles
                DB::raw('COALESCE(m.descripcion, "") as descripcion'),
                DB::raw('COALESCE(m.unidad, "") as unidad'),
                DB::raw('COALESCE(e.costo_promedio, 0) as costo_promedio'),
                DB::raw('COALESCE(m.costo_estandar, 0) as costo_estandar'),
                DB::raw('COALESCE(m.activo, 1) as activo'),
            ])
            ->orderBy('m.codigo')
            ->orderBy('a.nombre');

        $page = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'data' => $page->items(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'per_page'     => $page->perPage(),
                'total'        => $page->total(),
                'last_page'    => $page->lastPage(),
            ],
        ]);
    }
}

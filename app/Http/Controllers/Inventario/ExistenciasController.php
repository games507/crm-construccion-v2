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
     * - q: busca por codigo/sku/nombre/descripcion
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

        /**
         * Ajusta columnas según tu BD real:
         * - materiales: id, codigo, sku?, nombre, descripcion?, categoria?, stock_minimo?, empresa_id?
         * - existencias: material_id, almacen_id, cantidad/stock?, costo_promedio?
         * - almacenes: id, nombre, codigo?, empresa_id
         */

        $query = DB::table('materiales as m')
            // Si tus materiales tienen empresa_id, descomenta:
            // ->where('m.empresa_id', $empresaId)

            // Existencias por material (y opcional por almacén)
            ->leftJoin('existencias as e', function ($join) use ($almacenId) {
                $join->on('e.material_id', '=', 'm.id');
                if ($almacenId > 0) {
                    $join->where('e.almacen_id', '=', $almacenId);
                }
            })

            // Almacén (y filtramos por empresa)
            ->leftJoin('almacenes as a', function ($join) use ($empresaId) {
                $join->on('a.id', '=', 'e.almacen_id')
                     ->where('a.empresa_id', '=', $empresaId);
            })

            ->when($almacenId > 0, function ($qq) use ($almacenId) {
                // Si piden un almacén específico, aseguramos que venga de esa relación
                $qq->where('e.almacen_id', $almacenId);
            })

            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('m.codigo', 'like', "%{$q}%")
                      ->orWhere('m.nombre', 'like', "%{$q}%")
                      ->orWhere('m.descripcion', 'like', "%{$q}%")
                      ->orWhere('m.sku', 'like', "%{$q}%");
                });
            })

            ->select([
                'm.id as id',
                DB::raw('m.nombre as material'),
                DB::raw('COALESCE(m.codigo, "") as codigo'),
                DB::raw('COALESCE(m.sku, "") as sku'),
                DB::raw('COALESCE(m.categoria, "") as categoria'),
                DB::raw('COALESCE(a.nombre, "N/A") as almacen'),
                DB::raw('COALESCE(a.codigo, "") as almacen_codigo'),
                // OJO: cambia e.cantidad por e.stock si tu tabla usa stock
                DB::raw('COALESCE(e.cantidad, 0) as existencia'),
                DB::raw('COALESCE(m.stock_minimo, 0) as minimo'),
                DB::raw('COALESCE(e.costo_promedio, 0) as costo_promedio'),
            ])
            ->orderBy('m.nombre');

        // Paginación (mejor que limit 5000)
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

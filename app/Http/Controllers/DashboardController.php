<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\InvExistencia;
use App\Models\InvMovimiento;
use App\Models\Material;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $almacenes  = Almacen::count();
        $materiales = Material::count();

        $stockTotal = (float) InvExistencia::sum('stock');

        // Valor inventario = sum(stock * costo_promedio)
        $valorInventario = (float) InvExistencia::selectRaw('COALESCE(SUM(stock * costo_promedio),0) as v')
            ->value('v');

        // ✅ Total REAL de movimientos (para que no se descuadre con "Ver todos")
        $movimientosTotal = InvMovimiento::count();

        // ✅ En tablero solo 3 (los últimos)
        $ultimosMovs = InvMovimiento::with(['material', 'almacenOrigen', 'almacenDestino'])
            ->orderByDesc('fecha')->orderByDesc('id')
            ->limit(3)
            ->get();

        $topMateriales = InvExistencia::join('materiales', 'materiales.id', '=', 'inv_existencias.material_id')
            ->selectRaw('materiales.sku, materiales.descripcion, SUM(inv_existencias.stock) as stock, SUM(inv_existencias.stock * inv_existencias.costo_promedio) as valor')
            ->groupBy('materiales.sku', 'materiales.descripcion')
            ->orderByDesc('valor')
            ->limit(6)
            ->get();

        // ✅ Proyectos: sin romper el sistema (si no tienes modelo Proyectos, queda 0)
        $proyectos = 0;
        try {
            // Cambia \App\Models\Proyecto si tu modelo se llama distinto
            if (class_exists(\App\Models\Proyecto::class)) {
                $proyectos = \App\Models\Proyecto::count();
            } elseif (class_exists(\App\Models\Project::class)) {
                $proyectos = \App\Models\Project::count();
            }
        } catch (\Throwable $e) {
            $proyectos = 0;
        }

        $kpis = [
            'almacenes'          => $almacenes,
            'materiales'         => $materiales,
            'stock_total'        => $stockTotal,

            // ✅ lo seguimos mandando para mostrarlo en "Top materiales (valor)"
            'valor_inventario'   => $valorInventario,

            // ✅ para el cajón de proyectos
            'proyectos'          => $proyectos,

            // ✅ total real para el cajón de movimientos (no el limitado)
            'movimientos_total'  => $movimientosTotal,
        ];

        return view('dashboard', compact('kpis', 'ultimosMovs', 'topMateriales'));
    }
}

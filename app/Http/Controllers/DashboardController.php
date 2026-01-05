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
        $almacenes = Almacen::count();
        $materiales = Material::count();

        $stockTotal = (float) InvExistencia::sum('stock');

        // Valor inventario = sum(stock * costo_promedio)
        $valorInventario = (float) InvExistencia::selectRaw('COALESCE(SUM(stock * costo_promedio),0) as v')
            ->value('v');

        $ultimosMovs = InvMovimiento::with(['material','almacenOrigen','almacenDestino'])
            ->orderByDesc('fecha')->orderByDesc('id')
            ->limit(8)->get();

        $topMateriales = InvExistencia::join('materiales','materiales.id','=','inv_existencias.material_id')
            ->selectRaw('materiales.sku, materiales.descripcion, SUM(inv_existencias.stock) as stock, SUM(inv_existencias.stock * inv_existencias.costo_promedio) as valor')
            ->groupBy('materiales.sku','materiales.descripcion')
            ->orderByDesc('valor')
            ->limit(6)
            ->get();

        $kpis = [
            'almacenes' => $almacenes,
            'materiales' => $materiales,
            'stock_total' => $stockTotal,
            'valor_inventario' => $valorInventario,
        ];

        return view('dashboard', compact('kpis','ultimosMovs','topMateriales'));
    }
}

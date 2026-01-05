<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\InvMovimiento;
use App\Models\Material;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function form()
    {
        return view('inventario.kardex', [
            'materiales' => Material::orderBy('descripcion')->get(),
            'almacenes' => Almacen::orderBy('nombre')->get(),
            'rows' => [],
            'totales' => null,
        ]);
    }

    public function ver(Request $req)
    {
        $req->validate([
            'material_id' => ['required','integer'],
            'almacen_id' => ['required','integer'],
        ]);

        $material = Material::findOrFail($req->material_id);
        $almacenId = (int) $req->almacen_id;

        $movs = InvMovimiento::where('material_id', $material->id)
            ->where(function($q) use ($almacenId){
                $q->where('almacen_origen_id', $almacenId)
                  ->orWhere('almacen_destino_id', $almacenId);
            })
            ->orderBy('fecha')->orderBy('id')
            ->get();

        $rows = [];
        $saldo = 0.0;
        $entradas = 0.0;
        $salidas = 0.0;

        foreach ($movs as $m) {
            $signo = ($m->almacen_destino_id === $almacenId) ? +1 : -1;
            $cant = (float)$m->cantidad;
            $cantSigned = $signo * $cant;
            $saldo += $cantSigned;

            if ($cantSigned > 0) $entradas += $cantSigned;
            if ($cantSigned < 0) $salidas += abs($cantSigned);

            $rows[] = [
                'fecha' => $m->fecha?->format('Y-m-d'),
                'tipo' => $m->tipo,
                'entrada' => $cantSigned > 0 ? $cant : 0,
                'salida' => $cantSigned < 0 ? $cant : 0,
                'saldo' => $saldo,
                'ref' => $m->referencia,
            ];
        }

        $totales = [
            'entradas' => $entradas,
            'salidas' => $salidas,
            'saldo' => $saldo,
        ];

        return view('inventario.kardex', [
            'materiales' => Material::orderBy('descripcion')->get(),
            'almacenes' => Almacen::orderBy('nombre')->get(),
            'materialSel' => $material->id,
            'almacenSel' => $almacenId,
            'rows' => $rows,
            'totales' => $totales,
        ]);
    }
}

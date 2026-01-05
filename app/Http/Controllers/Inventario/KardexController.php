<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\InvMovimiento;
use App\Models\Material;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    /**
     * Muestra formulario (material + almacén).
     */
    public function index(Request $req)
    {
        $empresaId = (int) auth()->user()->empresa_id;

        if ($empresaId <= 0) {
            return view('inventario.kardex.index', [
                'materiales'  => collect(),
                'almacenes'   => collect(),
                'materialSel' => $req->query('material_id'),
                'almacenSel'  => $req->query('almacen_id'),
                'rows'        => [],
                'totales'     => null,
            ])->withErrors(['empresa_id' => 'Tu usuario no tiene empresa asignada.']);
        }

        $materiales = Material::where('empresa_id', $empresaId)
            ->orderBy('descripcion')
            ->get();

        $almacenes = Almacen::where('empresa_id', $empresaId)
            ->orderBy('nombre')
            ->get();

        return view('inventario.kardex.index', [
            'materiales'  => $materiales,
            'almacenes'   => $almacenes,
            'materialSel' => $req->query('material_id'),
            'almacenSel'  => $req->query('almacen_id'),
            'rows'        => [],
            'totales'     => null,
        ]);
    }

    /**
     * Genera el kardex.
     */
    public function kardexVer(Request $req)
    {
        $empresaId = (int) auth()->user()->empresa_id;

        if ($empresaId <= 0) {
            return redirect()->route('inventario.kardex')
                ->withErrors(['empresa_id' => 'Tu usuario no tiene empresa asignada.']);
        }

        $req->validate([
            'material_id' => ['required','integer'],
            'almacen_id'  => ['required','integer'],
        ]);

        // Material SOLO de esta empresa
        $material = Material::where('empresa_id', $empresaId)
            ->where('id', (int) $req->material_id)
            ->firstOrFail();

        // Almacén SOLO de esta empresa
        $almacenId = (int) $req->almacen_id;
        Almacen::where('empresa_id', $empresaId)
            ->where('id', $almacenId)
            ->firstOrFail();

        // Movimientos SOLO de esta empresa + material + almacén involucrado
        $movs = InvMovimiento::where('empresa_id', $empresaId)
            ->where('material_id', $material->id)
            ->where(function ($q) use ($almacenId) {
                $q->where('almacen_origen_id', $almacenId)
                  ->orWhere('almacen_destino_id', $almacenId);
            })
            ->orderBy('fecha')
            ->orderBy('id')
            ->get();

        $rows = [];
        $saldo = 0.0;
        $entradas = 0.0;
        $salidas = 0.0;

        foreach ($movs as $m) {
            $entra = ((int) $m->almacen_destino_id === $almacenId);
            $sale  = ((int) $m->almacen_origen_id === $almacenId);

            $entrada = $entra ? (float) $m->cantidad : 0.0;
            $salida  = $sale  ? (float) $m->cantidad : 0.0;

            $saldo = $saldo + $entrada - $salida;

            $entradas += $entrada;
            $salidas  += $salida;

            $rows[] = [
                'fecha'   => (string) $m->fecha,
                'tipo'    => (string) $m->tipo,
                'entrada' => $entrada,
                'salida'  => $salida,
                'saldo'   => $saldo,
                'ref'     => (string) ($m->referencia ?? ''),
            ];
        }

        $totales = [
            'entradas' => round($entradas, 4),
            'salidas'  => round($salidas, 4),
            'saldo'    => round($saldo, 4),
            'material' => $material->descripcion,
        ];

        return view('inventario.kardex.index', [
            'materiales'  => Material::where('empresa_id', $empresaId)->orderBy('descripcion')->get(),
            'almacenes'   => Almacen::where('empresa_id', $empresaId)->orderBy('nombre')->get(),
            'materialSel' => $material->id,
            'almacenSel'  => $almacenId,
            'rows'        => $rows,
            'totales'     => $totales,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Inventario;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\InvMovimiento;
use App\Models\Material;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Empresa;

class KardexController extends Controller
{
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        $scopeEmpresaId = (int) EmpresaScope::getId();
        $userEmpresaId  = (int) ($user->empresa_id ?? 0);

        $empresaId = $scopeEmpresaId > 0 ? $scopeEmpresaId : $userEmpresaId;

        if ($empresaId <= 0) {
            abort(403, 'Seleccione una empresa para continuar.');
        }

        return $empresaId;
    }

    public function index(Request $req)
    {
        $empresaId = $this->empresaIdOrAbort();

        return view('inventario.kardex.index', [
            'materiales'  => Material::where('empresa_id', $empresaId)->orderBy('descripcion')->get(),
            'almacenes'   => Almacen::where('empresa_id', $empresaId)->orderBy('nombre')->get(),
            'materialSel' => $req->query('material_id'),
            'almacenSel'  => $req->query('almacen_id'),
            'desde'       => $req->query('desde'),
            'hasta'       => $req->query('hasta'),
            'rows'        => collect(),
            'totales'     => null,
        ]);
    }

    public function kardexVer(Request $req)
    {
        $empresaId = $this->empresaIdOrAbort();

        $data = $req->validate([
            'material_id' => ['required', 'integer'],
            'almacen_id'  => ['required', 'integer'],
            'desde'       => ['nullable', 'date'],
            'hasta'       => ['nullable', 'date'],
        ]);

        $materialId = (int) $data['material_id'];
        $almacenId  = (int) $data['almacen_id'];
        $desde      = $data['desde'] ?? null;
        $hasta      = $data['hasta'] ?? null;

        $material = Material::where('empresa_id', $empresaId)
            ->where('id', $materialId)
            ->firstOrFail();

        $almacen = Almacen::where('empresa_id', $empresaId)
            ->where('id', $almacenId)
            ->firstOrFail();

        $query = InvMovimiento::where('empresa_id', $empresaId)
            ->where('material_id', $material->id)
            ->where(function ($q) use ($almacenId) {
                $q->where('almacen_origen_id', $almacenId)
                  ->orWhere('almacen_destino_id', $almacenId);
            });

        if ($desde) {
            $query->whereDate('fecha', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('fecha', '<=', $hasta);
        }

        $movs = $query
            ->with(['material', 'almacenOrigen', 'almacenDestino'])
            ->orderBy('fecha')
            ->orderBy('id')
            ->get();

        $rows = collect();

        $saldoCantidad = 0.0;
        $saldoValor = 0.0;

        $entradasCantidad = 0.0;
        $salidasCantidad = 0.0;
        $entradasValor = 0.0;
        $salidasValor = 0.0;

        foreach ($movs as $m) {
            $entra = ((int) $m->almacen_destino_id === $almacenId);
            $sale  = ((int) $m->almacen_origen_id === $almacenId);

            $cantidad = round((float) $m->cantidad, 4);
            $costoUnitario = round((float) ($m->costo_unitario ?? 0), 4);
            $valor = round($cantidad * $costoUnitario, 4);

            $entradaCantidad = $entra ? $cantidad : 0;
            $salidaCantidad  = $sale ? $cantidad : 0;

            $entradaValor = $entra ? $valor : 0;
            $salidaValor  = $sale ? $valor : 0;

            $saldoCantidad = round($saldoCantidad + $entradaCantidad - $salidaCantidad, 4);
            $saldoValor = round($saldoValor + $entradaValor - $salidaValor, 4);

            $entradasCantidad += $entradaCantidad;
            $salidasCantidad += $salidaCantidad;
            $entradasValor += $entradaValor;
            $salidasValor += $salidaValor;

            $meta = [];
            if (!empty($m->meta)) {
                $decoded = json_decode($m->meta, true);
                $meta = is_array($decoded) ? $decoded : [];
            }

            $rows->push([
                'fecha'             => $m->fecha,
                'tipo'              => (string) $m->tipo,
                'referencia'        => (string) ($m->referencia ?? ''),
                'detalle'           => $meta['descripcion'] ?? $meta['origen'] ?? '',
                'almacen_origen'    => $m->almacenOrigen->nombre ?? null,
                'almacen_destino'   => $m->almacenDestino->nombre ?? null,

                'entrada_cantidad'  => $entradaCantidad,
                'salida_cantidad'   => $salidaCantidad,
                'saldo_cantidad'    => $saldoCantidad,

                'costo_unitario'    => $costoUnitario,
                'entrada_valor'     => $entradaValor,
                'salida_valor'      => $salidaValor,
                'saldo_valor'       => $saldoValor,
            ]);
        }

        $totales = [
            'material'          => $material->descripcion,
            'almacen'           => $almacen->nombre,
            'entradas_cantidad' => round($entradasCantidad, 4),
            'salidas_cantidad'  => round($salidasCantidad, 4),
            'saldo_cantidad'    => round($saldoCantidad, 4),
            'entradas_valor'    => round($entradasValor, 2),
            'salidas_valor'     => round($salidasValor, 2),
            'saldo_valor'       => round($saldoValor, 2),
        ];

        return view('inventario.kardex.index', [
            'materiales'  => Material::where('empresa_id', $empresaId)->orderBy('descripcion')->get(),
            'almacenes'   => Almacen::where('empresa_id', $empresaId)->orderBy('nombre')->get(),
            'materialSel' => $material->id,
            'almacenSel'  => $almacenId,
            'desde'       => $desde,
            'hasta'       => $hasta,
            'rows'        => $rows,
            'totales'     => $totales,
        ]);
    }
    public function pdf(Request $req)
{
    $empresaId = $this->empresaIdOrAbort();

    $data = $req->validate([
        'material_id' => ['required', 'integer'],
        'almacen_id'  => ['required', 'integer'],
        'desde'       => ['nullable', 'date'],
        'hasta'       => ['nullable', 'date'],
    ]);

    $materialId = (int) $data['material_id'];
    $almacenId  = (int) $data['almacen_id'];

    $desde = $data['desde'] ?? null;
    $hasta = $data['hasta'] ?? null;

    $material = Material::where('empresa_id', $empresaId)
        ->where('id', $materialId)
        ->firstOrFail();

    $almacen = Almacen::where('empresa_id', $empresaId)
        ->where('id', $almacenId)
        ->firstOrFail();

    $empresa = Empresa::find($empresaId);

    $query = InvMovimiento::where('empresa_id', $empresaId)
        ->where('material_id', $materialId)
        ->where(function ($q) use ($almacenId) {
            $q->where('almacen_origen_id', $almacenId)
              ->orWhere('almacen_destino_id', $almacenId);
        });

    if ($desde) {
        $query->whereDate('fecha', '>=', $desde);
    }

    if ($hasta) {
        $query->whereDate('fecha', '<=', $hasta);
    }

    $movs = $query
        ->with(['almacenOrigen', 'almacenDestino'])
        ->orderBy('fecha')
        ->orderBy('id')
        ->get();

    $rows = [];

    $saldoCantidad = 0;
    $saldoValor = 0;

    foreach ($movs as $m) {

        $entra = ((int)$m->almacen_destino_id === $almacenId);
        $sale  = ((int)$m->almacen_origen_id === $almacenId);

        $cantidad = (float)$m->cantidad;
        $costo = (float)($m->costo_unitario ?? 0);
        $valor = $cantidad * $costo;

        $entrada = $entra ? $cantidad : 0;
        $salida  = $sale ? $cantidad : 0;

        $entradaValor = $entra ? $valor : 0;
        $salidaValor  = $sale ? $valor : 0;

        $saldoCantidad += ($entrada - $salida);
        $saldoValor += ($entradaValor - $salidaValor);

        $rows[] = [
            'fecha' => $m->fecha,
            'tipo' => $m->tipo,
            'referencia' => $m->referencia,
            'entrada' => $entrada,
            'salida' => $salida,
            'saldo' => $saldoCantidad,
            'costo' => $costo,
            'valor' => $valor,
            'saldo_valor' => $saldoValor,
        ];
    }

    $pdf = Pdf::loadView('inventario.kardex.pdf', [
        'empresa' => $empresa,
        'material' => $material,
        'almacen' => $almacen,
        'rows' => $rows,
        'desde' => $desde,
        'hasta' => $hasta,
    ])->setPaper('a4', 'landscape');

    return $pdf->stream(
        'kardex_'.$material->sku.'.pdf'
    );
}
}
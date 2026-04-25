<?php

namespace App\Http\Controllers\Admin;

use App\Exports\CuentasExport;
use App\Http\Controllers\Controller;
use App\Models\CuentaPago;
use App\Models\CuentaPorPagar;
use App\Models\Proyecto;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CuentaPorPagarController extends Controller
{
    private function empresaIdOrAbort(): int
    {
        $user = auth()->user();

        $scopeEmpresaId = (int) EmpresaScope::getId();
        $userEmpresaId  = (int) ($user->empresa_id ?? 0);

        $empresaId = $scopeEmpresaId ?: $userEmpresaId;

        abort_if($empresaId <= 0, 403, 'No hay empresa seleccionada o asociada al usuario.');

        return $empresaId;
    }

    private function proyectoValidoOrAbort(int $proyectoId): Proyecto
    {
        $empresaId = $this->empresaIdOrAbort();

        return Proyecto::where('empresa_id', $empresaId)
            ->findOrFail($proyectoId);
    }

    private function cuentaValidaOrAbort(int $cuentaId): CuentaPorPagar
    {
        $empresaId = $this->empresaIdOrAbort();

        return CuentaPorPagar::whereHas('proyecto', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->with(['proyecto', 'pagos'])
            ->findOrFail($cuentaId);
    }

    public function index()
    {
        $empresaId = $this->empresaIdOrAbort();

        $cuentas = CuentaPorPagar::with(['proyecto', 'pagos'])
            ->whereHas('proyecto', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->latest('id')
            ->get();

        return view('admin.cuentas.index', compact('cuentas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proyecto_id'       => ['required', 'integer', 'exists:proyectos,id'],
            'proveedor'         => ['required', 'string', 'max:180'],
            'descripcion'       => ['nullable', 'string'],
            'monto_total'       => ['required', 'numeric', 'min:0.01'],
            'fecha'             => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ]);

        $proyecto = $this->proyectoValidoOrAbort((int) $data['proyecto_id']);
        $montoTotal = (float) $data['monto_total'];

        CuentaPorPagar::create([
            'proyecto_id'       => $proyecto->id,
            'proveedor'         => $data['proveedor'],
            'descripcion'       => $data['descripcion'] ?? null,
            'monto_total'       => $montoTotal,
            'monto_pagado'      => 0,
            'saldo'             => $montoTotal,
            'fecha'             => $data['fecha'] ?? null,
            'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
            'estado'            => 'pendiente',
        ]);

        return back()->with('ok', '✅ Cuenta registrada correctamente.');
    }

    public function pagar(Request $request, $id)
    {
        $cuenta = $this->cuentaValidaOrAbort((int) $id);

        $data = $request->validate([
            'monto'       => ['required', 'numeric', 'min:0.01'],
            'fecha'       => ['nullable', 'date'],
            'observacion' => ['nullable', 'string', 'max:255'],
        ]);

        $monto = (float) $data['monto'];
        $saldoActual = (float) $cuenta->saldo;

        if ($monto > $saldoActual) {
            return back()->withErrors([
                'monto' => 'El pago no puede ser mayor al saldo pendiente.'
            ])->withInput();
        }

        DB::transaction(function () use ($cuenta, $data, $monto) {
            CuentaPago::create([
                'cuenta_id'   => $cuenta->id,
                'monto'       => $monto,
                'fecha'       => $data['fecha'] ?? now()->toDateString(),
                'observacion' => $data['observacion'] ?? 'Pago registrado',
            ]);

            $montoTotal = (float) $cuenta->monto_total;
            $nuevoMontoPagado = (float) $cuenta->monto_pagado + $monto;
            $nuevoSaldo = max($montoTotal - $nuevoMontoPagado, 0);

            $estado = 'pendiente';

            if ($nuevoSaldo <= 0) {
                $estado = 'pagado';
            } elseif ($nuevoMontoPagado > 0) {
                $estado = 'parcial';
            }

            $cuenta->update([
                'monto_pagado' => $nuevoMontoPagado,
                'saldo'        => $nuevoSaldo,
                'estado'       => $estado,
            ]);
        });

        return back()->with('ok', '✅ Pago registrado correctamente.');
    }

    public function destroy($id)
    {
        $cuenta = $this->cuentaValidaOrAbort((int) $id);

        if ($cuenta->pagos()->count() > 0) {
            return back()->withErrors([
                'cuenta' => 'No puedes eliminar una cuenta que ya tiene pagos registrados.'
            ]);
        }

        $cuenta->delete();

        return back()->with('ok', '✅ Cuenta eliminada correctamente.');
    }

    public function edit($id)
    {
        $cuenta = $this->cuentaValidaOrAbort((int) $id);

        return response()->json($cuenta);
    }

    public function update(Request $request, $id)
    {
        $cuenta = $this->cuentaValidaOrAbort((int) $id);

        $data = $request->validate([
            'proveedor'         => ['required', 'string', 'max:180'],
            'descripcion'       => ['nullable', 'string'],
            'fecha'             => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ]);

        $cuenta->update($data);

        return back()->with('ok', '✅ Cuenta actualizada correctamente.');
    }

    public function show($id)
    {
        $cuenta = $this->cuentaValidaOrAbort((int) $id);

        $cuenta->load([
            'proyecto',
            'pagos' => function ($q) {
                $q->latest('fecha')->latest('id');
            }
        ]);

        return view('admin.cuentas.show', compact('cuenta'));
    }

    public function exportar()
    {
        return Excel::download(
            new CuentasExport(),
            'cuentas_por_pagar_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
    public function reporteProveedores()
{
    $empresaId = $this->empresaIdOrAbort();

    $cuentas = \App\Models\CuentaPorPagar::with('proyecto')
        ->whereHas('proyecto', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        })
        ->get()
        ->groupBy('proveedor');

    return view('admin.cuentas.reporte_proveedores', compact('cuentas'));
}
public function flujoCaja()
{
    $empresaId = $this->empresaIdOrAbort();

    $cuentas = \App\Models\CuentaPorPagar::with('pagos')
        ->whereHas('proyecto', function ($q) use ($empresaId) {
            $q->where('empresa_id', $empresaId);
        })
        ->get();

    // INGRESOS (pagos realizados)
    $ingresos = $cuentas->flatMap->pagos;

    // EGRESOS (deuda generada)
    $egresos = $cuentas;

    // AGRUPAR POR MES
    $meses = [];

    foreach ($ingresos as $pago) {
        $mes = \Carbon\Carbon::parse($pago->fecha)->format('Y-m');
        $meses[$mes]['ingresos'] = ($meses[$mes]['ingresos'] ?? 0) + $pago->monto;
    }

    foreach ($egresos as $c) {
        if ($c->fecha) {
            $mes = \Carbon\Carbon::parse($c->fecha)->format('Y-m');
            $meses[$mes]['egresos'] = ($meses[$mes]['egresos'] ?? 0) + $c->monto_total;
        }
    }

    ksort($meses);

    return view('admin.cuentas.flujo_caja', compact('meses'));
}
}
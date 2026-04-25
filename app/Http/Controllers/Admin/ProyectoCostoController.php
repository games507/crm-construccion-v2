<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuentaPorPagar;
use App\Models\Proyecto;
use App\Models\ProyectoCosto;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProyectoCostoController extends Controller
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

    private function costoValidoOrAbort(int $costoId): ProyectoCosto
    {
        $empresaId = $this->empresaIdOrAbort();

        return ProyectoCosto::whereHas('proyecto', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->findOrFail($costoId);
    }

    private function buscarCuentaDelCosto(ProyectoCosto $costo): ?CuentaPorPagar
    {
        return CuentaPorPagar::where('proyecto_id', $costo->proyecto_id)
            ->where('origen_tipo', 'costo')
            ->where('origen_id', $costo->id)
            ->first();
    }

    private function debeCrearCuentaPorPagar(ProyectoCosto $costo): bool
    {
        $estadoPago = (string) ($costo->estado_pago ?? 'pendiente');

        return in_array($estadoPago, ['pendiente', 'parcial'], true);
    }

    private function sincronizarCuentaPorPagar(ProyectoCosto $costo): void
    {
        $cuenta = $this->buscarCuentaDelCosto($costo);
        $debeExistir = $this->debeCrearCuentaPorPagar($costo);

        $montoTotal = (float) $costo->monto;
        $estadoCosto = (string) $costo->estado_pago;

        if (!$debeExistir) {
            if ($cuenta) {
                if ($cuenta->pagos()->count() > 0) {
                    $cuenta->update([
                        'monto_total'  => $montoTotal,
                        'monto_pagado' => $montoTotal,
                        'saldo'        => 0,
                        'estado'       => 'pagado',
                    ]);
                } else {
                    $cuenta->delete();
                }
            }

            return;
        }

        $montoPagado = $cuenta ? (float) $cuenta->monto_pagado : 0;
        $montoPagado = min($montoPagado, $montoTotal);
        $saldo = max($montoTotal - $montoPagado, 0);

        if ($saldo <= 0) {
            $estado = 'pagado';
        } elseif ($montoPagado > 0 || $estadoCosto === 'parcial') {
            $estado = 'parcial';
        } else {
            $estado = 'pendiente';
        }

        $payload = [
            'proyecto_id'       => $costo->proyecto_id,
            'proveedor'         => $costo->proveedor ?: 'Sin proveedor',
            'descripcion'       => $costo->descripcion,
            'monto_total'       => $montoTotal,
            'monto_pagado'      => $montoPagado,
            'saldo'             => $saldo,
            'fecha'             => $costo->fecha,
            'fecha_vencimiento' => $costo->fecha,
            'estado'            => $estado,
            'origen_tipo'       => 'costo',
            'origen_id'         => $costo->id,
        ];

        if ($cuenta) {
            $cuenta->update($payload);
        } else {
            CuentaPorPagar::create($payload);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proyecto_id'    => ['required', 'integer', 'exists:proyectos,id'],
            'tipo'           => ['required', 'string', Rule::in(array_keys(ProyectoCosto::tipos()))],
            'categoria'      => ['nullable', 'string', 'max:120'],
            'descripcion'    => ['nullable', 'string'],
            'monto'          => ['required', 'numeric', 'min:0'],
            'fecha'          => ['nullable', 'date'],
            'proveedor'      => ['nullable', 'string', 'max:180'],
            'requiere_pago'  => ['nullable', 'boolean'],
            'estado_pago'    => ['required', Rule::in(array_keys(ProyectoCosto::estadosPago()))],
        ]);

        $proyecto = $this->proyectoValidoOrAbort((int) $data['proyecto_id']);

        $data['requiere_pago'] = $request->boolean('requiere_pago');
        $data['monto'] = (float) $data['monto'];

        $costo = ProyectoCosto::create($data);

        $this->sincronizarCuentaPorPagar($costo);

        return redirect()
            ->route('admin.proyectos.show', $proyecto->id)
            ->with('ok', '✅ Costo registrado correctamente.');
    }

    public function edit($id)
    {
        $costo = $this->costoValidoOrAbort((int) $id);

        return view('admin.proyectos.costos.edit', [
            'costo'    => $costo,
            'proyecto' => $costo->proyecto,
            'tipos'    => ProyectoCosto::tipos(),
            'estados'  => ProyectoCosto::estadosPago(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $costo = $this->costoValidoOrAbort((int) $id);

        $data = $request->validate([
            'tipo'           => ['required', 'string', Rule::in(array_keys(ProyectoCosto::tipos()))],
            'categoria'      => ['nullable', 'string', 'max:120'],
            'descripcion'    => ['nullable', 'string'],
            'monto'          => ['required', 'numeric', 'min:0'],
            'fecha'          => ['nullable', 'date'],
            'proveedor'      => ['nullable', 'string', 'max:180'],
            'requiere_pago'  => ['nullable', 'boolean'],
            'estado_pago'    => ['required', Rule::in(array_keys(ProyectoCosto::estadosPago()))],
        ]);

        $data['requiere_pago'] = $request->boolean('requiere_pago');
        $data['monto'] = (float) $data['monto'];

        $costo->update($data);

        $this->sincronizarCuentaPorPagar($costo);

        return redirect()
            ->route('admin.proyectos.show', $costo->proyecto_id)
            ->with('ok', '✅ Costo actualizado correctamente.');
    }

    public function destroy($id)
    {
        $costo = $this->costoValidoOrAbort((int) $id);
        $proyectoId = $costo->proyecto_id;

        $cuenta = $this->buscarCuentaDelCosto($costo);

        if ($cuenta) {
            if ($cuenta->pagos()->count() > 0) {
                $cuenta->update([
                    'origen_tipo' => null,
                    'origen_id'   => null,
                ]);
            } else {
                $cuenta->delete();
            }
        }

        $costo->delete();

        return redirect()
            ->route('admin.proyectos.show', $proyectoId)
            ->with('ok', '✅ Costo eliminado correctamente.');
    }
}
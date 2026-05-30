<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CuentaCobro;
use App\Models\CuentaPorCobrar;
use App\Models\Proyecto;
use App\Support\EmpresaScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentaPorCobrarController extends Controller
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

    private function cuentaValidaOrAbort(int $cuentaId): CuentaPorCobrar
    {
        $empresaId = $this->empresaIdOrAbort();

        return CuentaPorCobrar::whereHas('proyecto', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->with(['proyecto', 'cobros.user'])
            ->findOrFail($cuentaId);
    }

    public function index()
    {
        $empresaId = $this->empresaIdOrAbort();

        $cuentas = CuentaPorCobrar::with(['proyecto', 'cobros.user'])
            ->whereHas('proyecto', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->latest('id')
            ->get();

        $proyectos = Proyecto::where('empresa_id', $empresaId)
            ->orderBy('nombre')
            ->get();

        return view('admin.cobros.index', compact('cuentas', 'proyectos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'proyecto_id'       => ['required', 'integer', 'exists:proyectos,id'],
            'cliente'           => ['required', 'string', 'max:180'],
            'descripcion'       => ['nullable', 'string'],
            'monto_total'       => ['required', 'numeric', 'min:0.01'],
            'fecha'             => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ]);

        $proyecto = $this->proyectoValidoOrAbort((int) $data['proyecto_id']);
        $montoTotal = (float) $data['monto_total'];

        CuentaPorCobrar::create([
            'proyecto_id'       => $proyecto->id,
            'cliente'           => $data['cliente'],
            'descripcion'       => $data['descripcion'] ?? null,
            'monto_total'       => $montoTotal,
            'monto_cobrado'     => 0,
            'saldo'             => $montoTotal,
            'fecha'             => $data['fecha'] ?? null,
            'fecha_vencimiento' => $data['fecha_vencimiento'] ?? null,
            'estado'            => 'pendiente',
            'user_id'           => auth()->id(),
        ]);

        return back()->with('ok', '✅ Cuenta por cobrar registrada correctamente.');
    }

    public function cobrar(Request $request, $id)
    {
        $cuenta = $this->cuentaValidaOrAbort((int) $id);

        $data = $request->validate([
            'monto'       => ['required', 'numeric', 'min:0.01'],
            'observacion' => ['nullable', 'string', 'max:255'],
        ]);

        $monto = (float) $data['monto'];
        $saldoActual = (float) $cuenta->saldo;

        if ($monto > $saldoActual) {
            return back()->withErrors([
                'monto' => 'El cobro no puede ser mayor al saldo pendiente.'
            ])->withInput();
        }

        DB::transaction(function () use ($cuenta, $data, $monto) {
            CuentaCobro::create([
                'cuenta_id'   => $cuenta->id,
                'user_id'     => auth()->id(),
                'monto'       => $monto,
                'fecha'       => now(),
                'observacion' => $data['observacion'] ?? 'Cobro registrado',
            ]);

            $montoTotal = (float) $cuenta->monto_total;
            $nuevoMontoCobrado = (float) $cuenta->monto_cobrado + $monto;
            $nuevoSaldo = max($montoTotal - $nuevoMontoCobrado, 0);

            $estado = 'pendiente';

            if ($nuevoSaldo <= 0) {
                $estado = 'cobrado';
            } elseif ($nuevoMontoCobrado > 0) {
                $estado = 'parcial';
            }

            $cuenta->update([
                'monto_cobrado' => $nuevoMontoCobrado,
                'saldo'         => $nuevoSaldo,
                'estado'        => $estado,
            ]);
        });

        return back()->with('ok', '✅ Cobro registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $cuenta = $this->cuentaValidaOrAbort((int) $id);

        $data = $request->validate([
            'cliente'           => ['required', 'string', 'max:180'],
            'descripcion'       => ['nullable', 'string'],
            'fecha'             => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date'],
        ]);

        $cuenta->update($data);

        return back()->with('ok', '✅ Cuenta por cobrar actualizada correctamente.');
    }

    public function destroy($id)
    {
        $cuenta = $this->cuentaValidaOrAbort((int) $id);

        if ($cuenta->cobros()->count() > 0) {
            return back()->withErrors([
                'cuenta' => 'No puedes eliminar una cuenta que ya tiene cobros registrados.'
            ]);
        }

        $cuenta->delete();

        return back()->with('ok', '✅ Cuenta por cobrar eliminada correctamente.');
    }
}
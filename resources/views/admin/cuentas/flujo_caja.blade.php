@extends('layouts.base')
@section('title','Flujo de caja')

@section('content')

@php
  $totalIngresos = collect($meses)->sum('ingresos');
  $totalEgresos = collect($meses)->sum('egresos');
  $flujo = $totalIngresos - $totalEgresos;
@endphp

<div class="space-y-6">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">
        Flujo de caja
      </h1>
      <p class="text-sm text-slate-500 mt-1">
        Movimiento real de dinero: solo los pagos realizados cuentan como egreso.
      </p>
    </div>

    <a href="{{ route('admin.cuentas.index') }}"
       class="inline-flex items-center justify-center px-4 h-10 rounded-xl bg-slate-900 text-white text-sm font-bold hover:bg-slate-800">
      ← Volver
    </a>
  </div>

  {{-- NOTA CONTABLE --}}
  <div class="rounded-3xl border border-blue-200 bg-blue-50 p-4">
    <div class="font-black text-blue-800">
      Flujo de caja real
    </div>
    <p class="text-sm text-blue-700 mt-1">
      Las cuentas pendientes no se registran como egreso hasta que se realiza un pago.
    </p>
  </div>

  {{-- RESUMEN --}}
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

    <div class="rounded-3xl bg-emerald-50 border border-emerald-200 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-emerald-700 font-black">
        Ingresos reales
      </div>
      <div class="mt-2 text-2xl font-extrabold text-emerald-800">
        $ {{ number_format($totalIngresos, 2, '.', ',') }}
      </div>
    </div>

    <div class="rounded-3xl bg-rose-50 border border-rose-200 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-rose-700 font-black">
        Egresos reales
      </div>
      <div class="mt-2 text-2xl font-extrabold text-rose-800">
        $ {{ number_format($totalEgresos, 2, '.', ',') }}
      </div>
      <div class="mt-2 text-xs font-bold text-rose-700">
        Solo pagos realizados
      </div>
    </div>

    <div class="rounded-3xl bg-slate-50 border border-slate-200 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-slate-600 font-black">
        Flujo neto
      </div>
      <div class="mt-2 text-2xl font-extrabold {{ $flujo >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
        $ {{ number_format($flujo, 2, '.', ',') }}
      </div>
    </div>

    <div class="rounded-3xl bg-amber-50 border border-amber-200 p-5 shadow-sm">
      <div class="text-xs uppercase tracking-wide text-amber-700 font-black">
        Pendiente por pagar
      </div>
      <div class="mt-2 text-2xl font-extrabold text-amber-800">
        $ {{ number_format($totalPendiente ?? 0, 2, '.', ',') }}
      </div>
      <div class="mt-2 text-xs font-bold text-amber-700">
        No afecta caja hasta pagarse
      </div>
    </div>

  </div>

  {{-- ALERTA VENCIDO --}}
  @if(($totalVencido ?? 0) > 0)
    <div class="rounded-3xl border border-rose-200 bg-rose-50 p-4">
      <div class="font-black text-rose-800">
        ⚠️ Saldo vencido pendiente
      </div>
      <div class="text-sm text-rose-700 mt-1">
        Hay $ {{ number_format($totalVencido, 2, '.', ',') }} vencidos que todavía no han salido de caja.
      </div>
    </div>
  @endif

  {{-- TABLA --}}
  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="px-4 py-3 text-left">Mes</th>
          <th class="px-4 py-3 text-right">Ingresos reales</th>
          <th class="px-4 py-3 text-right">Egresos reales</th>
          <th class="px-4 py-3 text-right">Flujo neto</th>
          <th class="px-4 py-3 text-right">Pendiente</th>
          <th class="px-4 py-3 text-right">Vencido</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-slate-100">
        @forelse($meses as $mes => $data)
          @php
            $ing = (float) ($data['ingresos'] ?? 0);
            $egr = (float) ($data['egresos'] ?? 0);
            $pendiente = (float) ($data['pendiente'] ?? 0);
            $vencido = (float) ($data['vencido'] ?? 0);
            $flujoMes = $ing - $egr;
          @endphp

          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 font-black text-slate-900">
              {{ $mes }}
            </td>

            <td class="px-4 py-3 text-right text-emerald-700 font-bold">
              $ {{ number_format($ing, 2, '.', ',') }}
            </td>

            <td class="px-4 py-3 text-right text-rose-700 font-bold">
              $ {{ number_format($egr, 2, '.', ',') }}
            </td>

            <td class="px-4 py-3 text-right font-extrabold {{ $flujoMes >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
              $ {{ number_format($flujoMes, 2, '.', ',') }}
            </td>

            <td class="px-4 py-3 text-right text-amber-700 font-bold">
              $ {{ number_format($pendiente, 2, '.', ',') }}
            </td>

            <td class="px-4 py-3 text-right {{ $vencido > 0 ? 'text-rose-700 font-black' : 'text-slate-400 font-bold' }}">
              $ {{ number_format($vencido, 2, '.', ',') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
              No hay movimientos de flujo de caja registrados.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

</div>

@endsection
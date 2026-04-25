@extends('layouts.base')
@section('title','Flujo de caja')

@section('content')

<div class="space-y-6">

  <div class="flex justify-between items-center">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">
        Flujo de caja
      </h1>
      <p class="text-sm text-slate-500">
        Ingresos vs egresos por mes
      </p>
    </div>

    <a href="{{ route('admin.cuentas.index') }}"
       class="px-4 h-10 rounded-xl bg-slate-900 text-white text-sm font-bold flex items-center">
      ← Volver
    </a>
  </div>

  {{-- RESUMEN --}}
  @php
    $totalIngresos = collect($meses)->sum('ingresos');
    $totalEgresos = collect($meses)->sum('egresos');
    $flujo = $totalIngresos - $totalEgresos;
  @endphp

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

    <div class="bg-emerald-50 border border-emerald-200 p-5 rounded-2xl">
      <div class="text-xs text-emerald-700">Ingresos</div>
      <div class="text-2xl font-extrabold text-emerald-800">
        $ {{ number_format($totalIngresos,2) }}
      </div>
    </div>

    <div class="bg-rose-50 border border-rose-200 p-5 rounded-2xl">
      <div class="text-xs text-rose-700">Egresos</div>
      <div class="text-2xl font-extrabold text-rose-800">
        $ {{ number_format($totalEgresos,2) }}
      </div>
    </div>

    <div class="bg-slate-50 border p-5 rounded-2xl">
      <div class="text-xs text-slate-700">Flujo neto</div>
      <div class="text-2xl font-extrabold {{ $flujo >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
        $ {{ number_format($flujo,2) }}
      </div>
    </div>

  </div>

  {{-- TABLA --}}
  <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-4 py-3 text-left">Mes</th>
          <th class="px-4 py-3 text-right">Ingresos</th>
          <th class="px-4 py-3 text-right">Egresos</th>
          <th class="px-4 py-3 text-right">Flujo</th>
        </tr>
      </thead>

      <tbody>
        @foreach($meses as $mes => $data)

        @php
          $ing = $data['ingresos'] ?? 0;
          $egr = $data['egresos'] ?? 0;
          $flujoMes = $ing - $egr;
        @endphp

        <tr class="border-t hover:bg-slate-50">
          <td class="px-4 py-3 font-bold">{{ $mes }}</td>

          <td class="px-4 py-3 text-right text-emerald-700 font-bold">
            $ {{ number_format($ing,2) }}
          </td>

          <td class="px-4 py-3 text-right text-rose-700 font-bold">
            $ {{ number_format($egr,2) }}
          </td>

          <td class="px-4 py-3 text-right font-extrabold {{ $flujoMes >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
            $ {{ number_format($flujoMes,2) }}
          </td>
        </tr>

        @endforeach
      </tbody>
    </table>
  </div>

</div>

@endsection
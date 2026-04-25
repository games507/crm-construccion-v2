@extends('layouts.base')
@section('title','Reporte por proveedor')

@section('content')

<div class="space-y-6">

  <div class="flex justify-between items-center">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">
        Reporte por proveedor
      </h1>
      <p class="text-sm text-slate-500">
        Resumen financiero agrupado
      </p>
    </div>

    <a href="{{ route('admin.cuentas.index') }}"
       class="px-4 h-10 rounded-xl bg-slate-900 text-white text-sm font-bold flex items-center">
      ← Volver
    </a>
  </div>

  @foreach($cuentas as $proveedor => $items)

    @php
      $total = $items->sum('monto_total');
      $pagado = $items->sum('monto_pagado');
      $saldo = $items->sum('saldo');
    @endphp

    <div class="bg-white border rounded-2xl shadow-sm">

      {{-- HEADER --}}
      <div class="p-5 border-b flex justify-between items-center">

        <div>
          <h2 class="font-extrabold text-lg text-slate-900">
            {{ $proveedor }}
          </h2>
          <div class="text-sm text-slate-500">
            {{ $items->count() }} cuentas
          </div>
        </div>

        <div class="flex gap-6 text-sm">

          <div>
            <div class="text-xs text-slate-500">Total</div>
            <div class="font-bold">$ {{ number_format($total,2) }}</div>
          </div>

          <div>
            <div class="text-xs text-emerald-600">Pagado</div>
            <div class="font-bold text-emerald-700">
              $ {{ number_format($pagado,2) }}
            </div>
          </div>

          <div>
            <div class="text-xs text-rose-600">Saldo</div>
            <div class="font-bold text-rose-700">
              $ {{ number_format($saldo,2) }}
            </div>
          </div>

        </div>

      </div>

      {{-- TABLA --}}
      <div class="divide-y">

        @foreach($items as $c)

        <div class="flex justify-between p-4 text-sm hover:bg-slate-50">

          <div>
            <div class="font-bold text-slate-800">
              {{ $c->proyecto->nombre ?? '-' }}
            </div>
            <div class="text-xs text-slate-500">
              {{ $c->descripcion }}
            </div>
          </div>

          <div class="flex gap-6 text-right">

            <div>
              $ {{ number_format($c->monto_total,2) }}
            </div>

            <div class="text-emerald-600 font-bold">
              $ {{ number_format($c->monto_pagado,2) }}
            </div>

            <div class="text-rose-600 font-bold">
              $ {{ number_format($c->saldo,2) }}
            </div>

          </div>

        </div>

        @endforeach

      </div>

    </div>

  @endforeach

</div>

@endsection
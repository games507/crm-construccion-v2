@extends('layouts.base')
@section('title','Kárdex')

@section('content')
@php
  // ✅ Iconos (estilo inline SVG como vienes usando - "kraya")
  $kIcon = function(string $type): string {
    return match($type) {
      'entrada'  => '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0 4 4m-4-4-4 4"/></svg>',
      'salida'   => '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0 4-4m-4 4-4-4"/></svg>',
      'traslado' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h12m0 0-3-3m3 3-3 3M16.5 16.5h-12m0 0 3 3m-3-3 3-3"/></svg>',
      'ajuste'   => '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h3m-1.5 0v12m-6-6h12"/></svg>',
      default    => '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6h.01M12 12h.01M12 18h.01"/></svg>',
    };
  };

  // ✅ Colores por tipo
  $pillClass = function(string $type): string {
    return match($type) {
      'entrada'  => 'bg-emerald-50 text-emerald-700 border-emerald-200',
      'salida'   => 'bg-red-50 text-red-700 border-red-200',
      'traslado' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
      'ajuste'   => 'bg-purple-50 text-purple-700 border-purple-200',
      default    => 'bg-slate-50 text-slate-700 border-slate-200',
    };
  };
@endphp

<div class="max-w-[1100px] mx-auto space-y-4">

  {{-- Header --}}
  <div class="bg-white/80 backdrop-blur border border-slate-200/70 rounded-2xl shadow-sm">
    <div class="px-5 py-4 border-b border-slate-200/70 flex items-start justify-between gap-3 flex-wrap">
      <div class="min-w-0">
        <h2 class="text-lg font-black text-slate-900 leading-tight">Kárdex</h2>
        <div class="text-sm font-semibold text-slate-500 mt-1">
          Historial por <b>material</b> y <b>almacén</b>, con saldo acumulado.
        </div>
      </div>

      <a
        href="{{ route('inventario.movimientos') }}"
        class="inline-flex items-center justify-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
               bg-white border border-slate-200/70 hover:bg-slate-50 shadow-sm"
      >
        {!! $kIcon('traslado') !!}
        Ver movimientos
      </a>
    </div>

    <div class="p-5">
      {{-- Filtros --}}
      <form method="GET" action="{{ route('inventario.kardex.ver') }}">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

          {{-- Material --}}
          <div class="lg:col-span-8 space-y-1">
            <div class="text-sm font-extrabold text-slate-800">Material</div>
            <select
              name="material_id"
              required
              class="w-full h-11 rounded-xl bg-white border border-slate-200/70 shadow-sm
                     focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300
                     text-sm font-semibold text-slate-900 px-3"
            >
              @foreach($materiales as $m)
                <option value="{{ $m->id }}" @selected(($materialSel ?? '')==$m->id)>
                  {{ $m->sku }} — {{ $m->descripcion }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- Almacén --}}
          <div class="lg:col-span-4 space-y-1">
            <div class="text-sm font-extrabold text-slate-800">Almacén</div>
            <select
              name="almacen_id"
              required
              class="w-full h-11 rounded-xl bg-white border border-slate-200/70 shadow-sm
                     focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300
                     text-sm font-semibold text-slate-900 px-3"
            >
              @foreach($almacenes as $a)
                <option value="{{ $a->id }}" @selected(($almacenSel ?? '')==$a->id)>
                  {{ $a->nombre }}
                </option>
              @endforeach
            </select>
          </div>

        </div>

        <div class="mt-4 flex justify-end">
          <button
            class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                   bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm"
            type="submit"
          >
            {{-- icon: magnifying --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
              <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.35-5.4a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/>
            </svg>
            Ver Kárdex
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- KPIs --}}
  @if($totales)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white border rounded-2xl p-5 shadow-sm">
        <div class="text-xs font-extrabold text-slate-500 uppercase">Entradas</div>
        <div class="mt-2 text-2xl font-black text-slate-900">
          {{ number_format((float)$totales['entradas'], 0) }}
        </div>
      </div>

      <div class="bg-white border rounded-2xl p-5 shadow-sm">
        <div class="text-xs font-extrabold text-slate-500 uppercase">Salidas</div>
        <div class="mt-2 text-2xl font-black text-slate-900">
          {{ number_format((float)$totales['salidas'], 0) }}
        </div>
      </div>

      <div class="bg-white border rounded-2xl p-5 shadow-sm">
        <div class="text-xs font-extrabold text-slate-500 uppercase">Saldo</div>
        <div class="mt-2 text-2xl font-black text-slate-900">
          {{ number_format((float)$totales['saldo'], 2) }}
        </div>
      </div>
    </div>
  @endif

  {{-- Tabla --}}
  <div class="bg-white border rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-auto">
      <table class="min-w-[860px] w-full">
        <thead class="bg-slate-50">
          <tr>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600">Fecha</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600">Tipo</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600">Entrada</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600">Salida</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600">Saldo</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600">Referencia</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($rows as $r)
            @php
              $tipo = (string)($r['tipo'] ?? '');
              $pill = $pillClass($tipo);
            @endphp

            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3 text-sm font-semibold text-slate-800 whitespace-nowrap">
                {{ $r['fecha'] }}
              </td>

              {{-- ✅ Tipo con color + icono kraya --}}
              <td class="px-4 py-3">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold border {{ $pill }}">
                  {!! $kIcon($tipo) !!}
                  {{ strtoupper($tipo) }}
                </span>
              </td>

              <td class="px-4 py-3 text-sm font-semibold text-slate-800">
                {{ $r['entrada'] ? number_format((float)$r['entrada'], 0) : '—' }}
              </td>

              <td class="px-4 py-3 text-sm font-semibold text-slate-800">
                {{ $r['salida'] ? number_format((float)$r['salida'], 0) : '—' }}
              </td>

              <td class="px-4 py-3 text-sm font-black text-slate-900">
                {{ number_format((float)$r['saldo'], 2) }}
              </td>

              <td class="px-4 py-3 text-sm font-semibold text-slate-500">
                {{ $r['ref'] ?: '—' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-5 text-sm text-slate-500">
                Selecciona material y almacén para ver movimientos.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

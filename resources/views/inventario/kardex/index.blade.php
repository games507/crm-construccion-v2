@extends('layouts.base')
@section('title','Kárdex')

@section('content')
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
        {{-- icon: arrows-right-left --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h12m0 0-3-3m3 3-3 3M16.5 16.5h-12m0 0 3 3m-3-3 3-3"/>
        </svg>
        Ver movimientos
      </a>
    </div>

    <div class="p-5">

      {{-- Alerts --}}
      @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700">
          {{ $errors->first() }}
        </div>
      @endif

      {{-- Filtros --}}
      <form method="GET" action="{{ route('inventario.kardex.ver') }}">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

          {{-- Material --}}
          <div class="lg:col-span-8 space-y-1">
            <div class="text-sm font-extrabold text-slate-800">Material</div>
            <div class="relative">
              <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                {{-- icon: cube --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5 12 12.75 3 7.5m18 0-9-5.25L3 7.5m18 0v9.75A2.25 2.25 0 0 1 19.875 19.2L12 23.25 4.125 19.2A2.25 2.25 0 0 1 3 17.25V7.5"/>
                </svg>
              </div>

              <select
                name="material_id"
                required
                class="pl-10 w-full h-11 rounded-xl bg-white border border-slate-200/70 shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300
                       text-sm font-semibold text-slate-900"
              >
                @foreach($materiales as $m)
                  <option value="{{ $m->id }}" @selected(($materialSel ?? '')==$m->id)>
                    {{ $m->sku }} — {{ $m->descripcion }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="text-xs font-semibold text-slate-500">
              Selecciona el material que deseas consultar.
            </div>
          </div>

          {{-- Almacén --}}
          <div class="lg:col-span-4 space-y-1">
            <div class="text-sm font-extrabold text-slate-800">Almacén</div>
            <div class="relative">
              <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                {{-- icon: building-storefront --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15l1.5 6H3L4.5 3Zm1.5 6v12m12-12v12M9 21v-6h6v6"/>
                </svg>
              </div>

              <select
                name="almacen_id"
                required
                class="pl-10 w-full h-11 rounded-xl bg-white border border-slate-200/70 shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300
                       text-sm font-semibold text-slate-900"
              >
                @foreach($almacenes as $a)
                  <option value="{{ $a->id }}" @selected(($almacenSel ?? '')==$a->id)>
                    {{ $a->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="text-xs font-semibold text-slate-500">
              Se mostrará el saldo por este almacén.
            </div>
          </div>

        </div>

        <div class="mt-4 flex justify-end gap-2 flex-wrap">
          <button
            class="inline-flex items-center justify-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                   bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm"
            type="submit"
          >
            {{-- icon: magnifying-glass --}}
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
      <div class="bg-white/80 backdrop-blur border border-slate-200/70 rounded-2xl shadow-sm p-5">
        <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wide">Entradas</div>
        <div class="mt-2 text-2xl font-black text-slate-900">{{ number_format((float)$totales['entradas'],4) }}</div>
        <div class="mt-1 text-sm font-semibold text-slate-500">Total recibido en el almacén</div>
      </div>

      <div class="bg-white/80 backdrop-blur border border-slate-200/70 rounded-2xl shadow-sm p-5">
        <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wide">Salidas</div>
        <div class="mt-2 text-2xl font-black text-slate-900">{{ number_format((float)$totales['salidas'],4) }}</div>
        <div class="mt-1 text-sm font-semibold text-slate-500">Total despachado del almacén</div>
      </div>

      <div class="bg-white/80 backdrop-blur border border-slate-200/70 rounded-2xl shadow-sm p-5">
        <div class="text-xs font-extrabold text-slate-500 uppercase tracking-wide">Saldo</div>
        <div class="mt-2 text-2xl font-black text-slate-900">{{ number_format((float)$totales['saldo'],4) }}</div>
        <div class="mt-1 text-sm font-semibold text-slate-500">Existencia resultante</div>
      </div>
    </div>
  @endif

  {{-- Tabla --}}
  <div class="bg-white/80 backdrop-blur border border-slate-200/70 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-auto">
      <table class="min-w-[860px] w-full border-collapse">
        <thead class="bg-slate-50/70">
          <tr class="text-left">
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600 uppercase tracking-wide border-b border-slate-200/70">Fecha</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600 uppercase tracking-wide border-b border-slate-200/70">Tipo</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600 uppercase tracking-wide border-b border-slate-200/70">Entrada</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600 uppercase tracking-wide border-b border-slate-200/70">Salida</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600 uppercase tracking-wide border-b border-slate-200/70">Saldo</th>
            <th class="px-4 py-3 text-xs font-extrabold text-slate-600 uppercase tracking-wide border-b border-slate-200/70">Referencia</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-200/70">
          @forelse($rows as $r)
            @php
              $isIn = (float)$r['entrada'] > 0;
              $isOut = (float)$r['salida'] > 0;

              $pill = $isIn
                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                : ($isOut ? 'bg-red-50 text-red-700 border-red-200' : 'bg-indigo-50 text-indigo-700 border-indigo-200');
            @endphp

            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3 text-sm font-semibold text-slate-800 whitespace-nowrap">
                {{ $r['fecha'] }}
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold border {{ $pill }}">
                  {{ strtoupper($r['tipo']) }}
                </span>
              </td>

              <td class="px-4 py-3 text-sm font-semibold text-slate-800">
                {{ $r['entrada'] ? number_format((float)$r['entrada'],4) : '—' }}
              </td>

              <td class="px-4 py-3 text-sm font-semibold text-slate-800">
                {{ $r['salida'] ? number_format((float)$r['salida'],4) : '—' }}
              </td>

              <td class="px-4 py-3 text-sm font-black text-slate-900">
                {{ number_format((float)$r['saldo'],4) }}
              </td>

              <td class="px-4 py-3 text-sm font-semibold text-slate-500">
                {{ $r['ref'] ?: '—' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-5 text-sm font-semibold text-slate-500">
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

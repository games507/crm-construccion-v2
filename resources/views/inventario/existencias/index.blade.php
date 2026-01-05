@extends('layouts.base')
@section('title','Existencias')

@section('content')
@php
  $icon = function($name){
    if($name==='plus') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='search') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='x') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    if($name==='box') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="2"/><path d="M3.3 7L12 12l8.7-5" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M12 22V12" stroke="currentColor" stroke-width="2"/></svg>';
    return '';
  };

  $almacenSel = (int)($almacenId ?? 0);
  $qVal = trim((string)($q ?? ''));

  // Total robusto: paginador vs collection
  $totalRegistros = 0;
  if (is_object($existencias) && method_exists($existencias, 'total')) {
    $totalRegistros = (int) $existencias->total();
  } elseif (is_iterable($existencias)) {
    $totalRegistros = (int) count($existencias);
  }
@endphp

<div class="max-w-7xl mx-auto">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight flex items-center gap-2">
        <span class="text-slate-700">{!! $icon('box') !!}</span>
        Existencias
      </h1>
      <p class="text-sm text-slate-500 mt-1">
        Consulta por almacén y busca por código/SKU/descripcion.
      </p>
    </div>

    <div class="flex gap-2 flex-wrap">
      @can('inventario.crear')
        <a href="{{ route('inventario.movimientos.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                  bg-blue-600 text-white hover:bg-blue-700 shadow-sm">
          {!! $icon('plus') !!} Agregar existencia
        </a>
      @endcan
    </div>
  </div>

  {{-- Alerts --}}
  @if (session('ok'))
    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
      {{ session('ok') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      <div class="flex items-center gap-2 font-semibold">
        <span>{!! $icon('alert') !!}</span>
        <span>Hay errores</span>
      </div>
      <ul class="list-disc ml-6 mt-2 text-sm">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Filtros --}}
  <form method="GET" action="{{ route('inventario.existencias') }}"
        class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm p-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3">

      <div class="md:col-span-4">
        <label class="text-xs font-semibold text-slate-500">Almacén</label>
        <select name="almacen_id"
                class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                       focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none">
          <option value="0">— Todos —</option>
          @foreach($almacenes as $a)
            <option value="{{ $a->id }}" @selected((int)$a->id === $almacenSel)>
              {{ $a->codigo }} — {{ $a->nombre }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="md:col-span-6">
        <label class="text-xs font-semibold text-slate-500">Buscar</label>
        <div class="mt-2 flex gap-2">
          <div class="relative flex-1">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">{!! $icon('search') !!}</span>
            <input name="q" value="{{ $qVal }}" placeholder="Ej: MAT-001, SKU, cemento…"
                   class="w-full h-11 rounded-xl border border-slate-900/10 pl-11 pr-3
                          focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none">
          </div>

          <button type="submit"
                  class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                         bg-slate-900 text-white hover:bg-slate-800">
            {!! $icon('search') !!} Buscar
          </button>

          @if($almacenSel>0 || $qVal!=='')
            <a href="{{ route('inventario.existencias') }}"
               class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                      bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
              {!! $icon('x') !!} Limpiar
            </a>
          @endif
        </div>
      </div>

      <div class="md:col-span-2 flex items-end justify-end text-xs text-slate-500">
        <div class="text-right">
          <div class="font-semibold text-slate-700">Registros</div>
          <div>{{ $totalRegistros }}</div>
        </div>
      </div>

    </div>
  </form>

  {{-- Tabla --}}
  <div class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr class="text-left">
            <th class="px-4 py-3 font-semibold">Almacén</th>
            <th class="px-4 py-3 font-semibold">Material</th>
            <th class="px-4 py-3 font-semibold">Unidad</th>
            <th class="px-4 py-3 font-semibold text-right">Cantidad</th>
            <th class="px-4 py-3 font-semibold text-right">Costo prom.</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($existencias as $e)
            @php
              $a = $e->almacen;
              $m = $e->material;

              $matCode = $m->codigo ?? $m->sku ?? '—';
              $matDesc = $m->descripcion ?? '—';

              // Unidad: si existe relación (unidadRef) úsala, si no, cae a unidad (texto)
              $unidadTxt = '';
              if(isset($m->unidadRef) && $m->unidadRef){
                $unidadTxt = ($m->unidadRef->codigo ?? '') . ' - ' . ($m->unidadRef->descripcion ?? '');
                $unidadTxt = trim($unidadTxt, " -");
              }
              if($unidadTxt === ''){
                $unidadTxt = $m->unidad ?? '—';
              }
            @endphp

            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900">{{ $a->codigo ?? '—' }}</div>
                <div class="text-xs text-slate-500">{{ $a->nombre ?? '—' }}</div>
              </td>

              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900">{{ $matCode }}</div>
                <div class="text-xs text-slate-500">{{ $matDesc }}</div>
              </td>

              <td class="px-4 py-3 text-slate-700">
                {{ $unidadTxt }}
              </td>

              {{-- Cantidad: ENTERO (como pediste) --}}
              <td class="px-4 py-3 text-right font-extrabold">
                {{ number_format((float)$e->stock, 0) }}
              </td>

              {{-- Costo: 2 decimales (como pediste) --}}
              <td class="px-4 py-3 text-right">
                {{ number_format((float)$e->costo_promedio, 2) }}
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-4 py-10 text-center text-slate-500" colspan="5">
                No hay existencias para los filtros seleccionados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Paginación (solo si existe links()) --}}
  <div class="mt-4">
    @if(is_object($existencias) && method_exists($existencias, 'links'))
      {{ $existencias->links() }}
    @endif
  </div>

</div>
@endsection

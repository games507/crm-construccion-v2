@extends('layouts.base')
@section('title','Movimientos')

@section('content')
@php
  $icon = function($name){
    if($name==='plus') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='search') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='swap') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M16 3l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 7H10a4 4 0 0 0-4 4v0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 21l-4-4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 17h10a4 4 0 0 0 4-4v0" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    return '';
  };

  $tipoBadge = function($tipo){
    $tipo = (string)$tipo;
    if($tipo==='entrada')  return 'bg-emerald-100 text-emerald-800';
    if($tipo==='salida')   return 'bg-rose-100 text-rose-800';
    if($tipo==='traslado') return 'bg-sky-100 text-sky-800';
    if($tipo==='ajuste')   return 'bg-amber-100 text-amber-800';
    return 'bg-slate-100 text-slate-800';
  };
@endphp

<div class="max-w-7xl mx-auto">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight flex items-center gap-2">
        <span class="text-slate-700">{!! $icon('swap') !!}</span>
        Movimientos
      </h1>
      <p class="text-sm text-slate-500 mt-1">Entradas, salidas, traslados y ajustes de inventario.</p>
    </div>

    <div class="flex gap-2 flex-wrap">
      @can('inventario.crear')
        <a href="{{ route('inventario.movimientos.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                  bg-blue-600 text-white hover:bg-blue-700 shadow-sm">
          {!! $icon('plus') !!} Nuevo movimiento
        </a>
      @endcan
    </div>
  </div>

  {{-- Alerts --}}
  @if (session('ok'))
    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
      ✅ {{ session('ok') }}
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

  {{-- Tabla --}}
  <div class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr class="text-left">
            <th class="px-4 py-3 font-semibold">Fecha</th>
            <th class="px-4 py-3 font-semibold">Tipo</th>
            <th class="px-4 py-3 font-semibold">Material</th>
            <th class="px-4 py-3 font-semibold">Origen</th>
            <th class="px-4 py-3 font-semibold">Destino</th>
            <th class="px-4 py-3 font-semibold text-right">Cantidad</th>
            <th class="px-4 py-3 font-semibold text-right">Costo</th>
            <th class="px-4 py-3 font-semibold">Referencia</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($movs as $m)
            @php
              $mat = $m->material;
              $codigoMat = $mat->codigo ?? $mat->sku ?? '—';
              $descMat = $mat->descripcion ?? '—';
              $origen = $m->almacenOrigen;
              $destino = $m->almacenDestino;
            @endphp

            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3 whitespace-nowrap">
                {{ \Carbon\Carbon::parse($m->fecha)->format('Y-m-d') }}
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $tipoBadge($m->tipo) }}">
                  {{ ucfirst($m->tipo) }}
                </span>
              </td>

              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900">{{ $codigoMat }}</div>
                <div class="text-slate-500 text-xs">{{ $descMat }}</div>
              </td>

              <td class="px-4 py-3 text-slate-700">
                @if($origen)
                  <div class="font-semibold">{{ $origen->codigo }}</div>
                  <div class="text-xs text-slate-500">{{ $origen->nombre }}</div>
                @else
                  —
                @endif
              </td>

              <td class="px-4 py-3 text-slate-700">
                @if($destino)
                  <div class="font-semibold">{{ $destino->codigo }}</div>
                  <div class="text-xs text-slate-500">{{ $destino->nombre }}</div>
                @else
                  —
                @endif
              </td>

              <td class="px-4 py-3 text-right font-extrabold">
                {{ number_format((float)$m->cantidad, 0) }}
              </td>

              <td class="px-4 py-3 text-right">
                {{ number_format((float)($m->costo_unitario ?? 0), 2) }}
              </td>

              <td class="px-4 py-3 text-slate-700">
                {{ $m->referencia ?? '—' }}
              </td>
            </tr>
          @empty
            <tr>
              <td class="px-4 py-10 text-center text-slate-500" colspan="8">
                No hay movimientos registrados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-4 text-xs text-slate-500">
    Mostrando los últimos {{ count($movs) }} movimientos (máximo 200).
  </div>

</div>
@endsection

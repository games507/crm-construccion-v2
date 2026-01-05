@extends('layouts.base')
@section('title','Materiales')

@section('content')
@php
  $icon = function($name){
    if($name==='plus') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 5v14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='search') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='edit') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if($name==='box') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" stroke="currentColor" stroke-width="2"/><path d="M3.3 7L12 12l8.7-5" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M12 22V12" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    return '';
  };
@endphp

<div class="max-w-6xl mx-auto">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight flex items-center gap-2">
        <span class="text-slate-700">{!! $icon('box') !!}</span>
        Materiales
      </h1>
      <p class="text-sm text-slate-500 mt-1">Catálogo de materiales de tu empresa.</p>
    </div>

    <div class="flex gap-2 flex-wrap">
      @can('materiales.crear')
        <a href="{{ route('inventario.materiales.create') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                  bg-blue-600 text-white hover:bg-blue-700 shadow-sm">
          {!! $icon('plus') !!} Nuevo material
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

  @if (session('err'))
    <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 flex items-center gap-2">
      <span>{!! $icon('alert') !!}</span>
      <span>{{ session('err') }}</span>
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

  {{-- Search --}}
  <div class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm">
    <form class="p-4 flex flex-col md:flex-row gap-3 md:items-center" method="GET" action="{{ route('inventario.materiales') }}">
      <div class="flex-1 relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">{!! $icon('search') !!}</span>
        <input
          class="w-full h-11 pl-11 pr-3 rounded-xl border border-slate-900/10 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
          name="q"
          value="{{ $q ?? '' }}"
          placeholder="Buscar por descripción, código, sku o unidad…"
        >
      </div>

      <div class="flex gap-2">
        <button class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                       bg-slate-900 text-white hover:bg-slate-800 shadow-sm" type="submit">
          {!! $icon('search') !!} Buscar
        </button>

        @if(!empty($q))
          <a class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                    bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
             href="{{ route('inventario.materiales') }}">
            Limpiar
          </a>
        @endif
      </div>
    </form>
  </div>

  {{-- Table --}}
  <div class="mt-4 rounded-2xl border border-slate-900/10 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr class="text-left">
            <th class="px-4 py-3 font-semibold">Código / SKU</th>
            <th class="px-4 py-3 font-semibold">Descripción</th>
            <th class="px-4 py-3 font-semibold">Unidad</th>
            <th class="px-4 py-3 font-semibold text-center">Activo</th>
            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($materiales as $m)
            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900">
                  {{ $m->codigo ?? '—' }}
                </div>
                <div class="text-xs text-slate-500">
                  SKU: {{ $m->sku ?? '—' }}
                </div>
              </td>

              <td class="px-4 py-3">
                <div class="font-semibold">{{ $m->descripcion }}</div>
              </td>

              <td class="px-4 py-3">
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                  {{ $m->unidad ?? '—' }}
                </span>
              </td>

              <td class="px-4 py-3 text-center">
                @if((int)($m->activo ?? 1) === 1)
                  <span class="inline-flex items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                    Sí
                  </span>
                @else
                  <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                    No
                  </span>
                @endif
              </td>
<!-- boton de eliminar y editar -->
             <td class="px-4 py-3 text-right whitespace-nowrap">
  <x-action-icons
    :edit-url="route('inventario.materiales.edit', $m->id)"
    :delete-url="route('inventario.materiales.destroy', $m->id)"
    :can-edit="auth()->user()->can('materiales.editar')"
    :can-delete="auth()->user()->can('materiales.eliminar')"
    confirm="¿Eliminar este material?"
  />
</td>

            </tr>
          @empty
            <tr>
              <td class="px-4 py-8 text-center text-slate-500" colspan="5">
                No hay materiales registrados.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t border-slate-900/10">
      {{ $materiales->links() }}
    </div>
  </div>

</div>
@endsection

@extends('layouts.base')
@section('title','Editar Material')

@section('content')
@php
  // ICONOS estilo "Kraya" (inline)
  $icon = function($name){
    if($name==='back') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='save') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2"/><path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2"/><path d="M7 3v5h8" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='trash') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l1 16h10l1-16" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M10 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='x') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    if($name==='tag') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M20.59 13.41 12 22 2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M7 7h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    if($name==='box') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 8.5 12 13 3 8.5m18 0-9-5-9 5m18 0V19a2 2 0 0 1-1.1 1.79L12 23l-7.9-2.21A2 2 0 0 1 3 19V8.5" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
    return '';
  };

  $hasUnidades = isset($unidades) && $unidades->count() > 0;

  // 0/1 consistente, respeta old()
  $activo = (int) old('activo', (int)($material->activo ?? 1));

  // Aux texto badge
  $estadoTxt = $activo ? 'Activo' : 'Inactivo';
@endphp

<div class="max-w-4xl mx-auto space-y-4">

  {{-- HEADER --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div class="min-w-0">
      <div class="flex items-center gap-2 flex-wrap">
        <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Editar Material</h1>

        {{-- Badge estado --}}
        <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-extrabold
          {{ $activo ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
          <span class="h-2 w-2 rounded-full {{ $activo ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
          {{ $estadoTxt }}
        </span>
      </div>

      <p class="text-sm text-slate-500 mt-1">Actualiza los datos del material.</p>
      <p class="text-xs text-slate-500 mt-2">ID: <b class="text-slate-800">{{ $material->id }}</b></p>
    </div>

    <a class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
              bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
       href="{{ route('inventario.materiales') }}">
      {!! $icon('back') !!} Volver
    </a>
  </div>

  {{-- ALERTAS --}}
  @if(!$hasUnidades)
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 flex items-center gap-2">
      <span>{!! $icon('alert') !!}</span>
      <span>No hay unidades registradas. Debes crear al menos una unidad.</span>
    </div>
  @endif

  @if (session('err'))
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 flex items-center gap-2">
      <span>{!! $icon('alert') !!}</span>
      <span>{{ session('err') }}</span>
    </div>
  @endif

  @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      <div class="flex items-center gap-2 font-extrabold">
        <span>{!! $icon('alert') !!}</span>
        <span>Hay errores</span>
      </div>
      <ul class="list-disc ml-6 mt-2 text-sm font-semibold">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- CARD --}}
  <div class="rounded-2xl border border-slate-900/10 bg-white shadow-sm overflow-hidden">

    {{-- Top mini-info --}}
    <div class="px-5 py-4 border-b border-slate-900/10 bg-slate-50">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="flex items-center gap-2 text-sm font-bold text-slate-700">
          <span class="text-slate-400">{!! $icon('box') !!}</span>
          <span class="truncate">SKU: <b class="text-slate-900">{{ $material->sku ?? '—' }}</b></span>
        </div>
        <div class="flex items-center gap-2 text-sm font-bold text-slate-700">
          <span class="text-slate-400">{!! $icon('tag') !!}</span>
          <span class="truncate">Unidad actual: <b class="text-slate-900">{{ $material->unidad ?? '—' }}</b></span>
        </div>
        <div class="flex items-center gap-2 text-sm font-bold text-slate-700">
          <span class="h-2 w-2 rounded-full {{ $activo ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
          <span class="truncate">Estado: <b class="text-slate-900">{{ $estadoTxt }}</b></span>
        </div>
      </div>
    </div>

    {{-- FORM UPDATE --}}
    <form method="POST" action="{{ route('inventario.materiales.update',$material) }}" class="p-5">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Descripción --}}
        <div>
          <label class="text-xs font-extrabold text-slate-600">Descripción</label>
          <input
            class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 text-sm font-semibold
                   focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
            name="descripcion"
            value="{{ old('descripcion',$material->descripcion) }}"
            required
          >
        </div>

        {{-- Código --}}
        <div>
          <label class="text-xs font-extrabold text-slate-600">Código</label>
          <input
            class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 text-sm font-semibold
                   focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
            name="codigo"
            value="{{ old('codigo',$material->codigo) }}"
            placeholder="Ej: MAT-0001"
            required
          >
          <div class="mt-1 text-[11px] font-semibold text-slate-500">
            (El SKU se recalcula como <b>E{{ auth()->user()->empresa_id ?? 'X' }}-CÓDIGO</b>)
          </div>
        </div>

        {{-- Unidad --}}
        <div>
          <label class="text-xs font-extrabold text-slate-600">Unidad</label>
          <select
            class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 text-sm font-semibold
                   focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
            name="unidad_id" {{ $hasUnidades ? 'required' : 'disabled' }}
          >
            <option value="">— Seleccione —</option>
            @if($hasUnidades)
              @foreach($unidades as $u)
                <option value="{{ $u->id }}"
                  @selected((string)old('unidad_id', $material->unidad_id) === (string)$u->id)>
                  {{ $u->codigo }} - {{ $u->descripcion }}
                </option>
              @endforeach
            @endif
          </select>
        </div>

        {{-- Costo estándar --}}
        <div>
          <label class="text-xs font-extrabold text-slate-600">Costo estándar</label>
          <input
            type="number"
            step="0.01"
            min="0"
            class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 text-sm font-semibold
                   focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
            name="costo_estandar"
            value="{{ old('costo_estandar', $material->costo_estandar ?? 0) }}"
            placeholder="0.00"
          >
          <div class="mt-1 text-[11px] font-semibold text-slate-500">Se guarda con 2 decimales.</div>
        </div>

        {{-- Switch Activo (SIEMPRE ENVÍA 0/1) --}}
        <div class="md:col-span-2">
          <label class="flex items-center justify-between gap-3 rounded-2xl border border-slate-900/10 bg-white px-4 py-3">
            <div class="min-w-0">
              <div class="text-sm font-extrabold text-slate-900">Activo</div>
              <div class="text-xs font-semibold text-slate-500">
                Si lo desactivas, no se mostrará en selects/listados (mejor que borrar).
              </div>
            </div>

            <span class="flex items-center gap-3 shrink-0">
              <span class="text-xs font-extrabold {{ $activo ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $estadoTxt }}
              </span>

              <span class="relative inline-flex items-center">
                {{-- hidden => siempre manda 0 si está apagado --}}
                <input type="hidden" name="activo" value="0">

                <input
                  type="checkbox"
                  name="activo"
                  value="1"
                  class="peer sr-only"
                  {{ $activo ? 'checked' : '' }}
                >
                <span class="h-7 w-12 rounded-full bg-slate-200 peer-checked:bg-emerald-500 transition"></span>
                <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow-sm transition
                             peer-checked:translate-x-5"></span>
              </span>
            </span>
          </label>
        </div>

      </div>

      {{-- ACCIONES --}}
      <div class="mt-6 flex flex-wrap items-center justify-between gap-2">
        <a
          class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                 bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
          href="{{ route('inventario.materiales') }}"
        >
          {!! $icon('x') !!} Cancelar
        </a>

        <button
          class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                 bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm disabled:opacity-50"
          type="submit" {{ $hasUnidades ? '' : 'disabled' }}
        >
          {!! $icon('save') !!} Guardar cambios
        </button>
      </div>

    </form>
  </div>

  {{-- ELIMINAR (FUERA DEL FORM) --}}
  @can('materiales.eliminar')
    <div class="flex justify-end">
      <form method="POST" action="{{ route('inventario.materiales.destroy',$material) }}"
            onsubmit="return confirm('¿Seguro que deseas eliminar este material?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                       bg-white border border-red-200 text-red-700 hover:border-red-300 hover:bg-red-50 shadow-sm">
          {!! $icon('trash') !!} Eliminar
        </button>
      </form>
    </div>
  @endcan

</div>
@endsection

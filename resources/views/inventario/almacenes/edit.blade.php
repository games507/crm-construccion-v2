@extends('layouts.base')
@section('title','Editar Almacén')

@section('content')
@php
  $icon = function($name){
    if($name==='back') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='save') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2"/><path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2"/><path d="M7 3v5h8" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='x') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='trash') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l1 16h10l1-16" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M10 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    if($name==='ok') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M9 12.75 11.25 15 15 9.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22.5c5.799 0 10.5-4.701 10.5-10.5S17.799 1.5 12 1.5 1.5 6.201 1.5 12 6.201 22.5 12 22.5Z" stroke="currentColor" stroke-width="2"/></svg>';
    return '';
  };

  // Estado actual (respeta old())
  $activo = (int) old('activo', (int)($almacen->activo ?? 1));
@endphp

<div class="max-w-4xl mx-auto">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight">Editar Almacén</h1>
      <p class="text-sm text-slate-500 mt-1">Actualiza los datos del almacén.</p>
      <p class="text-xs text-slate-500 mt-2">ID: <b class="text-slate-800">{{ $almacen->id }}</b></p>
    </div>

    <a class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
              bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
       href="{{ route('inventario.almacenes') }}">
      {!! $icon('back') !!} Volver
    </a>
  </div>

  {{-- Alerts --}}
  @if (session('ok'))
    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 flex items-center gap-2">
      <span class="text-emerald-700">{!! $icon('ok') !!}</span>
      <span class="font-semibold">{{ session('ok') }}</span>
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

  {{-- Card --}}
  <div class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm">
    {{-- FORM PRINCIPAL (UPDATE) --}}
    <form method="POST" action="{{ route('inventario.almacenes.update',$almacen) }}" class="p-5">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
          <label class="text-xs font-semibold text-slate-500">Código</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 name="codigo" value="{{ old('codigo',$almacen->codigo) }}" required>
        </div>

        <div>
          <label class="text-xs font-semibold text-slate-500">Nombre</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 name="nombre" value="{{ old('nombre',$almacen->nombre) }}" required>
        </div>

        <div class="md:col-span-2">
          <label class="text-xs font-semibold text-slate-500">Ubicación (opcional)</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 name="ubicacion" value="{{ old('ubicacion',$almacen->ubicacion) }}">
        </div>

        {{-- Switch Activo (manda 0/1 siempre) --}}
        <div class="md:col-span-2">
          <label class="text-xs font-semibold text-slate-500 block mb-2">Estado</label>

          {{-- cuando OFF, manda 0 --}}
          <input type="hidden" name="activo" value="0">

          <label class="flex items-center justify-between gap-3 rounded-2xl border border-slate-900/10 bg-slate-50 px-4 py-3">
            <div>
              <div class="text-sm font-extrabold text-slate-900">Activo</div>
              <div class="text-xs font-semibold text-slate-500">
                Recomendado: si no se usa, desactívalo en vez de eliminarlo.
              </div>
            </div>

            <span class="relative inline-flex items-center">
              <input
                type="checkbox"
                name="activo"
                value="1"
                class="peer sr-only"
                {{ $activo ? 'checked' : '' }}
              >
              <span class="h-7 w-12 rounded-full bg-slate-300 peer-checked:bg-emerald-500 transition"></span>
              <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow-sm transition
                           peer-checked:translate-x-5"></span>
            </span>
          </label>

          <div class="mt-2 text-xs font-semibold text-slate-500">
            Nota: Si intentas eliminar un almacén con inventario, el sistema lo marcará como <b>INACTIVO</b> por seguridad.
          </div>
        </div>

      </div>

      {{-- Acciones --}}
      <div class="mt-6 flex flex-wrap items-center justify-between gap-2">
        <a class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                  bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
           href="{{ route('inventario.almacenes') }}">
          {!! $icon('x') !!} Cancelar
        </a>

        <button class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                       bg-blue-600 text-white hover:bg-blue-700 shadow-sm"
                type="submit">
          {!! $icon('save') !!} Guardar cambios
        </button>
      </div>

    </form>
  </div>

  {{-- FORM ELIMINAR (AFUERA DEL FORM PRINCIPAL) --}}
  @can('almacenes.eliminar')
    <div class="mt-3 flex justify-end">
      <form method="POST" action="{{ route('inventario.almacenes.destroy',$almacen) }}"
            onsubmit="return confirm('¿Deseas eliminar este almacén? Si tiene inventario, se marcará como INACTIVO.');">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                       bg-white border border-red-200 text-red-700 hover:border-red-300 hover:bg-red-50 shadow-sm">
          {!! $icon('trash') !!} Eliminar
        </button>
      </form>
    </div>
  @endcan

</div>
@endsection

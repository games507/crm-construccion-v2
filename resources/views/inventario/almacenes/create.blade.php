@extends('layouts.base')
@section('title','Nuevo Almacén')

@section('content')
@php
  $icon = function($name){
    if($name==='back') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='save') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2"/><path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2"/><path d="M7 3v5h8" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='x') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    return '';
  };
@endphp

<div class="max-w-4xl mx-auto">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight">Nuevo Almacén</h1>
      <p class="text-sm text-slate-500 mt-1">Registra un almacén para tu empresa.</p>
    </div>

    <a class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
              bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
       href="{{ route('inventario.almacenes') }}">
      {!! $icon('back') !!} Volver
    </a>
  </div>

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

  <div class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm">
    <form method="POST" action="{{ route('inventario.almacenes.store') }}" class="p-5">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
          <label class="text-xs font-semibold text-slate-500">Código</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 name="codigo" value="{{ old('codigo') }}" placeholder="Ej: ALM-001" required>
        </div>

        <div>
          <label class="text-xs font-semibold text-slate-500">Nombre</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 name="nombre" value="{{ old('nombre') }}" placeholder="Ej: Almacén Principal" required>
        </div>

        <div class="md:col-span-2">
          <label class="text-xs font-semibold text-slate-500">Ubicación (opcional)</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 name="ubicacion" value="{{ old('ubicacion') }}" placeholder="Ej: Bodega 2 - Planta baja">
        </div>

        <div class="flex items-end">
          <label class="inline-flex items-center gap-2 font-semibold text-slate-700">
            <input type="checkbox" name="activo" value="1"
                   class="w-5 h-5 rounded border-slate-300"
                   {{ old('activo',1) ? 'checked' : '' }}>
            Activo
          </label>
        </div>

      </div>

      <div class="mt-6 flex flex-wrap justify-end gap-2">
        <a class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                  bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
           href="{{ route('inventario.almacenes') }}">
          {!! $icon('x') !!} Cancelar
        </a>

        <button class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                       bg-blue-600 text-white hover:bg-blue-700 shadow-sm"
                type="submit">
          {!! $icon('save') !!} Guardar
        </button>
      </div>

    </form>
  </div>

</div>
@endsection

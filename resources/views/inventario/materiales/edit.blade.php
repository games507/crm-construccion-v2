@extends('layouts.base')
@section('title','Editar Material')

@section('content')
@php
  $icon = function($name){
    if($name==='back') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='save') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2"/><path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2"/><path d="M7 3v5h8" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='trash') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l1 16h10l1-16" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M10 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='x') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    return '';
  };

  $hasUnidades = isset($unidades) && $unidades->count() > 0;

  // ✅ activo 0/1 consistente
  $activo = (int) old('activo', (int)($material->activo ?? 1));
@endphp

<div class="max-w-4xl mx-auto">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight">Editar Material</h1>
      <p class="text-sm text-slate-500 mt-1">Actualiza los datos del material.</p>
      <p class="text-xs text-slate-500 mt-2">ID: <b class="text-slate-800">{{ $material->id }}</b></p>
    </div>

    <a class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
              bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
       href="{{ route('inventario.materiales') }}">
      {!! $icon('back') !!} Volver
    </a>
  </div>

  @if(!$hasUnidades)
    <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 flex items-center gap-2">
      <span>{!! $icon('alert') !!}</span>
      <span>No hay unidades registradas. Debes crear al menos una unidad.</span>
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

  <div class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm">
    <form method="POST" action="{{ route('inventario.materiales.update',$material) }}" class="p-5">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
          <label class="text-xs font-semibold text-slate-500">Descripción</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 name="descripcion" value="{{ old('descripcion',$material->descripcion) }}" required>
        </div>

        <div>
          <label class="text-xs font-semibold text-slate-500">Código (opcional)</label>
          <input class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                        focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                 name="codigo" value="{{ old('codigo',$material->codigo) }}" placeholder="Ej: MAT-0001">
        </div>

        <div>
          <label class="text-xs font-semibold text-slate-500">Unidad</label>
          <select class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3
                         focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none"
                  name="unidad_id" {{ $hasUnidades ? 'required' : 'disabled' }}>
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

        {{-- ✅ Switch Activo (igual estándar de Almacén) --}}
        <div class="md:col-span-2">
          <label class="flex items-center justify-between gap-3 rounded-2xl border border-slate-900/10 bg-slate-50 px-4 py-3">
            <div>
              <div class="text-sm font-extrabold text-slate-900">Activo</div>
              <div class="text-xs font-semibold text-slate-500">
                Si lo desactivas, no se mostrará en selects/listados (mejor que borrar).
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
        </div>

      </div>

      <div class="mt-6 flex flex-wrap items-center justify-between gap-2">
        <a class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                  bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
           href="{{ route('inventario.materiales') }}">
          {!! $icon('x') !!} Cancelar
        </a>

        <div class="flex gap-2 flex-wrap">

          @can('materiales.eliminar')
            <form method="POST" action="{{ route('inventario.materiales.destroy',$material) }}"
                  onsubmit="return confirm('¿Seguro que deseas eliminar este material?')">
              @csrf
              @method('DELETE')
              <button type="submit"
                      class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                             bg-white border border-red-200 text-red-700 hover:border-red-300 hover:bg-red-50 shadow-sm">
                {!! $icon('trash') !!} Eliminar
              </button>
            </form>
          @endcan

          <button class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
                         bg-blue-600 text-white hover:bg-blue-700 shadow-sm disabled:opacity-50"
                  type="submit" {{ $hasUnidades ? '' : 'disabled' }}>
            {!! $icon('save') !!} Guardar cambios
          </button>

        </div>
      </div>

    </form>
  </div>

</div>
@endsection

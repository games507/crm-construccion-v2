@extends('layouts.base')
@section('title','Editar Proyecto')

@section('content')
@php
  // valores seguros
  $estadoSel = (string) old('estado', (string)($proyecto->estado ?? 'planeado'));
  $activoSel = (int) old('activo', (int)($proyecto->activo ?? 1));

  $fi = old('fecha_inicio', $proyecto->fecha_inicio?->format('Y-m-d'));
  $ff = old('fecha_fin', $proyecto->fecha_fin?->format('Y-m-d'));
@endphp

<div class="max-w-4xl mx-auto">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Editar Proyecto</h1>
      <p class="text-sm text-slate-500 mt-1">
        Actualiza datos generales y estado del proyecto.
      </p>
    </div>

    <a href="{{ route('admin.proyectos') }}"
       class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold
              bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
      ← Volver
    </a>
  </div>

  {{-- Alerts --}}
  @if (session('ok'))
    <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
      ✅ {{ session('ok') }}
    </div>
  @endif

  @if (session('err'))
    <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      ❌ {{ session('err') }}
    </div>
  @endif

  {{-- Errores --}}
  @if ($errors->any())
    <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      ❌ {{ $errors->first() }}
    </div>
  @endif

  {{-- Form --}}
  <form method="POST" action="{{ route('admin.proyectos.update', $proyecto->id) }}"
        class="mt-5 rounded-2xl border border-slate-900/10 bg-white shadow-sm p-6">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

      {{-- Empresa --}}
      @can('empresas.ver')
        <div class="md:col-span-12">
          <label class="text-xs font-semibold text-slate-500">Empresa</label>
          <select name="empresa_id" required
                  class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                         focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">
            <option value="">— Selecciona —</option>
            @foreach($empresas as $e)
              <option value="{{ $e->id }}" @selected((int)old('empresa_id', $proyecto->empresa_id)==(int)$e->id)>
                {{ $e->nombre }}
              </option>
            @endforeach
          </select>
        </div>
      @endcan

      {{-- Código --}}
      <div class="md:col-span-6">
        <label class="text-xs font-semibold text-slate-500">Código</label>
        <input name="codigo" value="{{ old('codigo', $proyecto->codigo) }}" placeholder="PR-001"
               class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                      focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">
      </div>

      {{-- Estado --}}
      <div class="md:col-span-6">
        <label class="text-xs font-semibold text-slate-500">Estado</label>
        <select name="estado" required
                class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                       focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">
          <option value="planeado"     @selected($estadoSel==='planeado')>Planeado</option>
          <option value="en_ejecucion" @selected($estadoSel==='en_ejecucion')>En ejecución</option>
          <option value="pausado"      @selected($estadoSel==='pausado')>Pausado</option>
          <option value="finalizado"   @selected($estadoSel==='finalizado')>Finalizado</option>
        </select>
      </div>

      {{-- Nombre --}}
      <div class="md:col-span-12">
        <label class="text-xs font-semibold text-slate-500">Nombre</label>
        <input name="nombre" value="{{ old('nombre', $proyecto->nombre) }}" required
               class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                      focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">
      </div>

      {{-- Ubicación --}}
      <div class="md:col-span-12">
        <label class="text-xs font-semibold text-slate-500">Ubicación</label>
        <input name="ubicacion" value="{{ old('ubicacion', $proyecto->ubicacion) }}"
               class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                      focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">
      </div>

      {{-- Fechas --}}
      <div class="md:col-span-6">
        <label class="text-xs font-semibold text-slate-500">Fecha inicio</label>
        <input type="date" name="fecha_inicio" value="{{ $fi }}"
               class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                      focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">
      </div>

      <div class="md:col-span-6">
        <label class="text-xs font-semibold text-slate-500">Fecha fin</label>
        <input type="date" name="fecha_fin" value="{{ $ff }}"
               class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                      focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">
      </div>

      {{-- Presupuesto --}}
      <div class="md:col-span-6">
        <label class="text-xs font-semibold text-slate-500">Presupuesto</label>
        <input type="number" step="0.01" name="presupuesto"
               value="{{ old('presupuesto', $proyecto->presupuesto) }}"
               class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 bg-white
                      focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 outline-none">
      </div>

      {{-- Activo --}}
      <div class="md:col-span-6 flex items-end">
        <label class="flex items-center gap-3 h-11 w-full rounded-xl
                      border border-slate-900/10 bg-white px-4
                      shadow-sm cursor-pointer">
          <input type="checkbox" name="activo" value="1"
                 class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                 {{ $activoSel ? 'checked' : '' }}>
          <span class="font-semibold text-slate-700">Proyecto activo</span>
          <span class="text-xs text-slate-500">(desmarca para inactivo)</span>
        </label>
      </div>

    </div>

    {{-- Acciones --}}
    <div class="mt-6 flex justify-end gap-2">
      <a href="{{ route('admin.proyectos') }}"
         class="inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold
                bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
        Cancelar
      </a>

      <button type="submit"
              class="inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold
                     bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
        Guardar cambios
      </button>
    </div>

  </form>
</div>
@endsection

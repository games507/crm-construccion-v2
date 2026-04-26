@extends('layouts.base')
@section('title','Editar Tarea')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

  {{-- HEADER --}}
  <div class="flex justify-between items-center">
    <div>
      <h1 class="text-2xl font-extrabold text-slate-900">
        Editar Tarea
      </h1>
      <p class="text-sm text-slate-500 mt-1">
        Modifica los datos de la tarea
      </p>
    </div>

    <a href="{{ route('admin.proyectos.show', $tarea->proyecto_id) }}"
       class="bg-white border px-4 py-2 rounded-xl text-sm shadow">
       Volver
    </a>
  </div>

  {{-- ALERTAS --}}
  @if ($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="bg-white p-6 rounded-2xl shadow border">

    {{-- FORM PRINCIPAL --}}
    <form method="POST" action="{{ route('admin.proyectos.tareas.updateFull', $tarea->id) }}">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="md:col-span-2">
          <label class="text-sm font-semibold text-slate-600">Nombre</label>
          <input type="text" name="nombre"
            value="{{ old('nombre', $tarea->nombre) }}"
            class="mt-1 w-full h-11 rounded-xl border px-3">
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-600">Fase</label>
          <select name="fase_id" class="mt-1 w-full h-11 rounded-xl border px-3">
            <option value="">Sin fase</option>
            @foreach($fases as $fase)
              <option value="{{ $fase->id }}" @selected($tarea->fase_id == $fase->id)>
                {{ $fase->nombre }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-600">Responsable</label>
          <select name="responsable_id" class="mt-1 w-full h-11 rounded-xl border px-3">
            <option value="">Sin responsable</option>
            @foreach($usuarios as $u)
              <option value="{{ $u->id }}" @selected($tarea->responsable_id == $u->id)>
                {{ $u->{$nameField} ?? ('Usuario '.$u->id) }}
              </option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-600">Estado</label>
          <select name="estado" class="mt-1 w-full h-11 rounded-xl border px-3">
            <option value="pendiente" @selected($tarea->estado=='pendiente')>Pendiente</option>
            <option value="en_proceso" @selected($tarea->estado=='en_proceso')>En proceso</option>
            <option value="finalizada" @selected($tarea->estado=='finalizada')>Finalizada</option>
            <option value="pausada" @selected($tarea->estado=='pausada')>Pausada</option>
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-600">% Avance</label>
          <input type="number" step="0.01" name="porcentaje"
            value="{{ old('porcentaje', $tarea->porcentaje) }}"
            class="mt-1 w-full h-11 rounded-xl border px-3">
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-600">Fecha Inicio</label>
          <input type="date" name="fecha_inicio"
            value="{{ old('fecha_inicio', optional($tarea->fecha_inicio)->format('Y-m-d')) }}"
            class="mt-1 w-full h-11 rounded-xl border px-3">
        </div>

        <div>
          <label class="text-sm font-semibold text-slate-600">Fecha Fin</label>
          <input type="date" name="fecha_fin"
            value="{{ old('fecha_fin', optional($tarea->fecha_fin)->format('Y-m-d')) }}"
            class="mt-1 w-full h-11 rounded-xl border px-3">
        </div>

        <div class="md:col-span-2">
          <label class="text-sm font-semibold text-slate-600">Descripción</label>
          <textarea name="descripcion" rows="4"
            class="mt-1 w-full rounded-xl border px-3 py-2">{{ old('descripcion', $tarea->descripcion) }}</textarea>
        </div>

      </div>

      <div class="mt-6 flex justify-end gap-2">

        <a href="{{ route('admin.proyectos.show', $tarea->proyecto_id) }}"
           class="bg-slate-200 px-4 py-2 rounded-xl text-sm">
           Cancelar
        </a>

        <button type="submit"
          class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-semibold">
          Guardar cambios
        </button>

      </div>

    </form>

    {{-- 🔴 FORM ELIMINAR SEPARADO --}}
    <form method="POST"
          action="{{ route('admin.proyectos.tareas.destroy', $tarea->id) }}"
          class="mt-4"
          onsubmit="return confirm('¿Eliminar esta tarea?')">
      @csrf
      @method('DELETE')

      <button class="bg-red-600 text-white px-4 py-2 rounded-xl text-sm">
        Eliminar tarea
      </button>
    </form>

  </div>

</div>

@endsection
@extends('layouts.base')
@section('title','Editar Costo')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
        Editar costo
      </h1>
      <p class="text-sm text-slate-500 mt-1">
        Proyecto: {{ $proyecto->nombre }}
      </p>
    </div>

    <a href="{{ route('admin.proyectos.show', $proyecto->id) }}"
       class="inline-flex items-center justify-center rounded-xl px-4 h-11 text-sm font-semibold bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
      Volver
    </a>
  </div>

  @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      ❌ {{ $errors->first() }}
    </div>
  @endif

  <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5">
    <form method="POST" action="{{ route('admin.proyectos.costos.update', $costo->id) }}">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <select name="tipo" class="h-11 rounded-xl border border-slate-300 px-3">
          <option value="">Tipo de costo</option>
          @foreach($tipos as $key => $label)
            <option value="{{ $key }}" @selected(old('tipo', $costo->tipo) == $key)>{{ $label }}</option>
          @endforeach
        </select>

        <input type="text" name="categoria" value="{{ old('categoria', $costo->categoria) }}" placeholder="Categoría"
          class="h-11 rounded-xl border border-slate-300 px-3">

        <input type="text" name="proveedor" value="{{ old('proveedor', $costo->proveedor) }}" placeholder="Proveedor / acreedor"
          class="h-11 rounded-xl border border-slate-300 px-3">

        <input type="number" step="0.01" name="monto" value="{{ old('monto', $costo->monto) }}" placeholder="Monto"
          class="h-11 rounded-xl border border-slate-300 px-3">

        <input type="date" name="fecha"
          value="{{ old('fecha', optional($costo->fecha)->format('Y-m-d')) }}"
          class="h-11 rounded-xl border border-slate-300 px-3">

        <select name="estado_pago" class="h-11 rounded-xl border border-slate-300 px-3">
          @foreach($estados as $key => $label)
            <option value="{{ $key }}" @selected(old('estado_pago', $costo->estado_pago) == $key)>{{ $label }}</option>
          @endforeach
        </select>

        <label class="h-11 rounded-xl border border-slate-300 px-3 flex items-center gap-2 text-sm text-slate-700 bg-white">
          <input type="checkbox" name="requiere_pago" value="1" class="rounded border-slate-300"
                 @checked(old('requiere_pago', $costo->requiere_pago))>
          Requiere pago
        </label>

        <button class="bg-indigo-600 text-white rounded-xl h-11 font-semibold px-4">
          Actualizar costo
        </button>
      </div>

      <div class="mt-3">
        <textarea name="descripcion" rows="4" placeholder="Descripción del costo"
          class="w-full rounded-xl border border-slate-300 px-3 py-3">{{ old('descripcion', $costo->descripcion) }}</textarea>
      </div>
    </form>
  </div>

</div>
@endsection
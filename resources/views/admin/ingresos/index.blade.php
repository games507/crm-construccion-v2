@extends('layouts.base')
@section('title','Ingresos')

@section('content')

<div class="space-y-5">

  <h1 class="text-xl font-extrabold">Ingresos</h1>

  {{-- FORM --}}
  <div class="bg-white p-4 rounded-2xl border shadow-sm">
    <form method="POST" action="{{ route('admin.ingresos.store') }}" class="flex gap-2 flex-wrap">
      @csrf

      <select name="proyecto_id" class="h-9 border rounded-xl px-2 text-xs">
        <option value="">Proyecto</option>
        @foreach($proyectos as $p)
          <option value="{{ $p->id }}">{{ $p->nombre }}</option>
        @endforeach
      </select>

      <input type="text" name="descripcion" placeholder="Descripción"
        class="h-9 border rounded-xl px-2 text-xs">

      <input type="number" step="0.01" name="monto"
        class="h-9 border rounded-xl px-2 text-xs" placeholder="Monto">

      <input type="date" name="fecha"
        class="h-9 border rounded-xl px-2 text-xs">

      <button class="px-3 h-9 bg-indigo-600 text-white rounded-xl text-xs font-bold">
        Guardar
      </button>

    </form>
  </div>

  {{-- TABLA --}}
  <div class="bg-white rounded-2xl border shadow-sm">
    <table class="w-full text-xs">
      <thead class="bg-slate-50">
        <tr>
          <th class="p-2 text-left">Fecha</th>
          <th class="p-2">Proyecto</th>
          <th class="p-2">Descripción</th>
          <th class="p-2 text-right">Monto</th>
        </tr>
      </thead>

      <tbody>
        @foreach($ingresos as $i)
        <tr class="border-t">
          <td class="p-2">{{ $i->fecha->format('Y-m-d') }}</td>
          <td class="p-2 text-center">{{ $i->proyecto->nombre ?? '-' }}</td>
          <td class="p-2">{{ $i->descripcion }}</td>
          <td class="p-2 text-right font-bold text-emerald-700">
            $ {{ number_format($i->monto,2) }}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

</div>

@endsection
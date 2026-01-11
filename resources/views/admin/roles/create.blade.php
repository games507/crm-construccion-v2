@extends('layouts.base')
@section('title','Nuevo Rol')

@section('content')
@php
  // si no llega $grupos, lo armamos aquí para evitar undefined
  if (!isset($grupos)) {
    $grupos = \Spatie\Permission\Models\Permission::orderBy('name')
      ->get()
      ->groupBy(function ($p) {
        $name = (string) $p->name;
        $parts = explode('.', $name);
        return $parts[0] ?? 'otros';
      });
  }
  $grupos = $grupos ?? collect();

  // permisos marcados (cuando vuelve con errors)
  $selectedPerms = old('permissions', []);

  // iconos estilo "Kraya"
  $icon = function($name){
    if($name==='back') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='save') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2"/><path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2"/><path d="M7 3v5h8" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='check') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4.5 12.75 10.5 18.75 19.5 5.25" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if($name==='minus') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    return '';
  };
@endphp

<div class="max-w-6xl mx-auto space-y-4">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Nuevo Rol</h1>
      <p class="text-sm text-slate-500 mt-1">Crea un rol y asigna permisos por módulo.</p>
    </div>

    <a href="{{ route('admin.roles') }}"
       class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
              bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
      <span class="h-5 w-5">{!! $icon('back') !!}</span>
      Volver
    </a>
  </div>

  {{-- Errors --}}
  @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      <div class="flex items-center gap-2 font-extrabold">
        <span class="h-5 w-5">{!! $icon('alert') !!}</span>
        <span>Hay errores</span>
      </div>
      <ul class="list-disc ml-6 mt-2 text-sm font-semibold">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Card --}}
  <div class="rounded-2xl border border-slate-900/10 bg-white shadow-sm overflow-hidden">

    <div class="px-5 py-4 border-b border-slate-900/10 bg-slate-50">
      <div class="text-sm font-extrabold text-slate-900">Datos del rol</div>
      <div class="text-xs font-semibold text-slate-500">Define nombre y permisos.</div>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}" class="p-5 space-y-5">
      @csrf

      {{-- Nombre + Botones marcar --}}
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-6">
          <label class="text-xs font-extrabold text-slate-600">Nombre del Rol</label>
          <input
            class="mt-2 w-full h-11 rounded-xl border border-slate-900/10 px-3 text-sm font-semibold
                   focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
            name="name"
            value="{{ old('name') }}"
            placeholder="Ej: Supervisor, Bodega, Consulta"
            required
          >
        </div>

        <div class="md:col-span-6 flex items-end justify-end gap-2">
          <button type="button"
                  class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                         bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
                  onclick="toggleAll(true)">
            <span class="h-5 w-5">{!! $icon('check') !!}</span>
            Marcar todo
          </button>

          <button type="button"
                  class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                         bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
                  onclick="toggleAll(false)">
            <span class="h-5 w-5">{!! $icon('minus') !!}</span>
            Desmarcar todo
          </button>
        </div>
      </div>

      {{-- Permisos --}}
      <div class="rounded-2xl border border-slate-900/10 bg-slate-50 p-4">
        <div class="flex items-center justify-between gap-3 flex-wrap">
          <div>
            <div class="font-extrabold text-slate-900">Permisos</div>
            <div class="text-sm text-slate-500">Asigna permisos por módulo (grupo).</div>
          </div>
        </div>

        @if($grupos->count() === 0)
          <div class="mt-3 text-sm font-semibold text-slate-500">
            No hay permisos registrados. Ejecuta tu seeder de permisos/roles.
          </div>
        @else
          <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach($grupos as $key => $items)
              @php
                $keyStr = is_string($key) && $key !== '' ? $key : 'otros';
                $titulo = ucfirst($keyStr);

                // CLAVE SEGURA PARA CSS/JS (sin puntos/espacios)
                $cssKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $keyStr);
              @endphp

              <div class="rounded-2xl border border-slate-900/10 bg-white p-4">
                <div class="flex items-center justify-between gap-3">
                  <div class="font-extrabold text-slate-900">{{ $titulo }}</div>

                  <button type="button"
                          class="inline-flex items-center gap-2 rounded-xl px-3 h-10 text-sm font-extrabold
                                 bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
                          onclick="toggleGroup('{{ $cssKey }}')">
                    <span class="h-5 w-5">{!! $icon('check') !!}</span>
                    Marcar grupo
                  </button>
                </div>

                <div class="mt-3 space-y-2">
                  @foreach($items as $perm)
                    @php
                      $p = is_object($perm) ? $perm->name : (string)$perm;
                      $checked = in_array($p, $selectedPerms, true);
                    @endphp

                    <label class="flex items-start gap-3 rounded-xl px-3 py-2 hover:bg-slate-50 cursor-pointer">
                      <input
                        class="perm perm-{{ $cssKey }} mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200"
                        type="checkbox"
                        name="permissions[]"
                        value="{{ $p }}"
                        {{ $checked ? 'checked' : '' }}
                      >
                      <span class="text-sm font-semibold text-slate-800 break-words">{{ $p }}</span>
                    </label>
                  @endforeach
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Acciones --}}
      <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('admin.roles') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                  bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
          Cancelar
        </a>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                       bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
          <span class="h-5 w-5">{!! $icon('save') !!}</span>
          Guardar rol
        </button>
      </div>

    </form>
  </div>
</div>

@push('scripts')
<script>
function toggleAll(on){
  document.querySelectorAll('.perm').forEach(c => c.checked = !!on);
}
function toggleGroup(cssKey){
  const items = document.querySelectorAll('.perm-' + cssKey);
  const anyOff = Array.from(items).some(c => !c.checked);
  items.forEach(c => c.checked = anyOff);
}
</script>
@endpush
@endsection

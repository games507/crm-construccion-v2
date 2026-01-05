@extends('layouts.base')
@section('title','Editar Rol')

@section('content')
@php
  // ✅ [SE MANTIENE] Si no llega $rolePerms, lo armamos
  $rolePerms = $rolePerms ?? (isset($role) ? $role->permissions()->pluck('name')->toArray() : []);

  // ✅ [SE MANTIENE] Si no llega $grupos, lo armamos aquí mismo (evita Undefined variable $grupos)
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

  // Iconos (Heroicons)
  $iconBack = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
  </svg>';

  $iconSave = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M17 21H7a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h7l5 5v9a2 2 0 0 1-2 2Z"/>
    <path stroke-linecap="round" stroke-linejoin="round" d="M7 21V13h10v8"/>
    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h7"/>
  </svg>';

  $iconCheck = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 10.5 18.75 19.5 5.25"/>
  </svg>';

  $iconMinus = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
  </svg>';
@endphp

<div class="max-w-6xl mx-auto space-y-4">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Editar Rol</h1>
      <p class="text-sm text-slate-500 mt-1">Actualiza el rol y sus permisos por módulo.</p>
    </div>

    <a href="{{ route('admin.roles') }}"
       class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
              bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
      <span class="h-4 w-4">{!! $iconBack !!}</span>
      Volver
    </a>
  </div>

  {{-- Errors --}}
  @if ($errors->any())
    <x-ui.alert type="err">
      ❌ {{ $errors->first() }}
    </x-ui.alert>
  @endif

  {{-- Form --}}
  <x-ui.card>
    <form method="POST" action="{{ route('admin.roles.update',$role) }}" class="space-y-5">
      @csrf
      @method('PUT')

      {{-- ✅ [CAMBIO A] Campo Nombre del rol (sin input-wrap / sin estilos inline) --}}
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-6">
          <x-ui.input
            label="Nombre del Rol"
            name="name"
            required
            :value="old('name', $role->name)"
            placeholder="Ej: Admin, Supervisor, Bodega"
          />
        </div>

        <div class="md:col-span-6 flex items-end justify-end gap-2">
          {{-- ✅ [CAMBIO B] Botones marcar/desmarcar (estilo Krayin) --}}
          <button type="button"
                  class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                         bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
                  onclick="toggleAll(true)">
            <span class="h-4 w-4">{!! $iconCheck !!}</span>
            Marcar todo
          </button>

          <button type="button"
                  class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                         bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
                  onclick="toggleAll(false)">
            <span class="h-4 w-4">{!! $iconMinus !!}</span>
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

        <div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
          @forelse($grupos as $key => $items)
            @php
              $keyStr = is_string($key) && $key !== '' ? $key : 'otros';
              $titulo = ucfirst($keyStr);
              $cssKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $keyStr);
            @endphp

            <div class="rounded-2xl border border-slate-900/10 bg-white p-4">
              <div class="flex items-center justify-between gap-3">
                <div class="font-extrabold text-slate-900">{{ $titulo }}</div>

                {{-- ✅ [CAMBIO C] Marcar grupo como botón compacto --}}
                <button type="button"
                        class="inline-flex items-center gap-2 rounded-xl px-3 h-10 text-sm font-semibold
                               bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
                        onclick="toggleGroup('{{ $cssKey }}')">
                  <span class="h-4 w-4">{!! $iconCheck !!}</span>
                  Marcar grupo
                </button>
              </div>

              <div class="mt-3 space-y-2">
                @foreach($items as $permModel)
                  @php
                    $permName = is_object($permModel) ? $permModel->name : (string)$permModel;
                    $checked = in_array($permName, old('permissions',$rolePerms), true);
                  @endphp

                  {{-- ✅ checkbox UI limpia --}}
                  <label class="flex items-start gap-3 rounded-xl px-3 py-2 hover:bg-slate-50 cursor-pointer">
                    <input
                      class="perm perm-{{ $cssKey }} mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200"
                      type="checkbox"
                      name="permissions[]"
                      value="{{ $permName }}"
                      {{ $checked ? 'checked' : '' }}
                    >
                    <span class="text-sm font-semibold text-slate-800 break-words">
                      {{ $permName }}
                    </span>
                  </label>
                @endforeach
              </div>
            </div>

          @empty
            <div class="text-slate-500">No hay permisos registrados.</div>
          @endforelse
        </div>
      </div>

      {{-- Footer actions --}}
      <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('admin.roles') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                  bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
          Cancelar
        </a>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                       bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
          <span class="h-4 w-4">{!! $iconSave !!}</span>
          Guardar cambios
        </button>
      </div>
    </form>
  </x-ui.card>
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

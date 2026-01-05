@extends('layouts.base')
@section('title','Roles')

@section('content')
@php
  $q = $q ?? request('q','');

  $iconSearch = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
  </svg>';

  $iconPlus = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
  </svg>';

  $iconX = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
  </svg>';

  // TIP N+1: si el controller trajo withCount('permissions') lo usamos.
  $hasWithCount = isset($roles) && $roles->count() && isset($roles->first()->permissions_count);
@endphp

<div class="max-w-6xl mx-auto space-y-4">

  {{-- Header + CTA --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Roles</h1>
      <p class="text-sm text-slate-500 mt-1">Administra roles y sus permisos.</p>
    </div>

    @can('roles.crear')
      <a href="{{ route('admin.roles.create') }}"
         class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
        <span class="h-4 w-4">{!! $iconPlus !!}</span>
        Nuevo rol
      </a>
    @endcan
  </div>

  {{-- Alert OK --}}
  @if(session('ok'))
    <x-ui.alert type="ok">
      ✅ {{ session('ok') }}
    </x-ui.alert>
  @endif

  {{-- ✅ [CAMBIO A] Formulario (sin input-wrap / sin estilos inline) --}}
  <x-ui.card>
    <form method="GET" action="{{ route('admin.roles') }}">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-3">

        <div class="md:col-span-9">
          <x-ui.input
            name="q"
            :value="$q"
            label="Buscar"
            placeholder="Buscar rol (ej: Admin, Supervisor)"
            :icon="$iconSearch"
          />
        </div>

        <div class="md:col-span-3 flex items-end justify-end gap-2">
          <x-ui.button variant="dark" type="submit" :icon="$iconSearch">
            Buscar
          </x-ui.button>

          @if(trim($q) !== '')
            <a href="{{ route('admin.roles') }}"
               class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                      bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
              <span class="h-4 w-4">{!! $iconX !!}</span>
              Limpiar
            </a>
          @endif
        </div>

      </div>
    </form>
  </x-ui.card>

  {{-- Tabla --}}
  <div class="rounded-2xl border border-slate-900/10 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr class="text-left">
            <th class="px-4 py-3 font-semibold">Rol</th>
            <th class="px-4 py-3 font-semibold w-[220px]">Permisos</th>
            <th class="px-4 py-3 font-semibold w-[180px] text-right">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($roles as $r)
            @php
              $permsCount = $hasWithCount
                ? (int)$r->permissions_count
                : (method_exists($r,'permissions') ? (int)$r->permissions()->count() : 0);

              $isAdmin = strtolower(trim($r->name)) === 'admin';
            @endphp

            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3 flex-wrap">
                  <x-ui.badge :variant="$isAdmin ? 'success' : 'indigo'">
                    <span class="h-2.5 w-2.5 rounded-full {{ $isAdmin ? 'bg-emerald-500' : 'bg-indigo-500' }}"></span>
                    {{ $r->name }}
                  </x-ui.badge>

                  <span class="text-xs font-extrabold text-slate-400">
                    ID: {{ $r->id }}
                  </span>
                </div>

                <div class="text-xs text-slate-500 mt-2">
                  {{ $isAdmin ? 'Rol con acceso total (si lo asignas a un usuario).' : 'Rol configurable por permisos.' }}
                </div>
              </td>

              <td class="px-4 py-3">
                <x-ui.badge variant="neutral">
                  <span class="text-slate-900">{{ $permsCount }}</span>
                  <span class="opacity-70 font-extrabold">permisos</span>
                </x-ui.badge>
              </td>

              <td class="px-4 py-3 text-right whitespace-nowrap">
                {{-- ✅ [CAMBIO B] Botón "Editar" -> iconos (editar + eliminar) --}}
                <x-action-icons
                  :edit-url="route('admin.roles.edit', $r)"
                  :delete-url="route('admin.roles.destroy', $r)"
                  :can-edit="auth()->user()->can('roles.editar')"
                  :can-delete="auth()->user()->can('roles.eliminar')"
                  confirm="¿Eliminar este rol?"
                />
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="px-4 py-10 text-center text-slate-500">
                No hay roles.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Paginación --}}
  <div>
    @if(method_exists($roles,'links'))
      {{ $roles->links() }}
    @endif
  </div>

</div>
@endsection

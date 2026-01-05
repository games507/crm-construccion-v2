@extends('layouts.base')
@section('title','Usuarios')

@section('content')
@php
  $q = $q ?? request('q','');
  $empresaId = $empresaId ?? request('empresa_id','');
  $empresas = $empresas ?? \App\Models\Empresa::orderBy('nombre')->get();

  $iconSearch = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/>
  </svg>';

  $iconX = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
  </svg>';

  $iconPlus = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
  </svg>';
@endphp

<div class="max-w-6xl mx-auto space-y-4">

  {{-- Header + CTA --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Usuarios</h1>
      <p class="text-sm text-slate-500 mt-1">
        Gestión de usuarios, roles, empresa asignada y estado.
      </p>
    </div>

    @can('usuarios.crear')
      <a href="{{ route('admin.usuarios.create') }}"
         class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
        <span class="h-4 w-4">{!! $iconPlus !!}</span>
        Nuevo usuario
      </a>
    @endcan
  </div>

  @if(session('ok'))
    <x-ui.alert type="ok">
      ✅ {{ session('ok') }}
    </x-ui.alert>
  @endif

  {{-- Filtros --}}
  <x-ui.card>
    <form method="GET" action="{{ route('admin.usuarios') }}">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-3">

        <div class="md:col-span-5">
          <x-ui.input
            name="q"
            :value="$q"
            label="Buscar"
            placeholder="Buscar por nombre o correo"
            :icon="$iconSearch"
          />
        </div>

        <div class="md:col-span-4">
          <x-ui.select name="empresa_id" label="Empresa">
            <option value="">— Todas las empresas —</option>
            @foreach($empresas as $e)
              <option value="{{ $e->id }}" @selected((string)$empresaId === (string)$e->id)>
                {{ $e->nombre }}
              </option>
            @endforeach
          </x-ui.select>
        </div>

        <div class="md:col-span-3 flex items-end justify-end gap-2">
          <x-ui.button variant="dark" type="submit" :icon="$iconSearch">
            Filtrar
          </x-ui.button>

          @if(trim($q) !== '' || (string)$empresaId !== '')
            <a href="{{ route('admin.usuarios') }}"
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
            <th class="px-4 py-3 font-semibold">Usuario</th>
            <th class="px-4 py-3 font-semibold">Empresa</th>
            <th class="px-4 py-3 font-semibold">Rol</th>
            <th class="px-4 py-3 font-semibold">Estado</th>
            <th class="px-4 py-3 font-semibold text-right">Acciones</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($usuarios as $u)
            @php
              $role = $u->roles->first()?->name;
              $isActive = (bool)($u->activo ?? false);
            @endphp

            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3">
                <div class="font-extrabold text-slate-900">{{ $u->name }}</div>
                <div class="text-xs text-slate-500">{{ $u->email }}</div>
              </td>

              <td class="px-4 py-3 text-slate-700">
                {{ $u->empresa?->nombre ?? '—' }}
              </td>

              <td class="px-4 py-3">
                <x-ui.badge variant="indigo">
                  {{ $role ?: '—' }}
                </x-ui.badge>
              </td>

              <td class="px-4 py-3">
                <x-ui.badge :variant="$isActive ? 'success' : 'danger'">
                  <span class="h-2.5 w-2.5 rounded-full {{ $isActive ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                  {{ $isActive ? 'ACTIVO' : 'INACTIVO' }}
                </x-ui.badge>
              </td>

              <td class="px-4 py-3 text-right whitespace-nowrap">
                @can('usuarios.editar')
                  <x-action-icons
                    :edit-url="route('admin.usuarios.edit', $u)"
                    :delete-url="null"
                    :can-edit="true"
                    :can-delete="false"
                  />
                @else
                  <span class="text-slate-400">—</span>
                @endcan
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-10 text-center text-slate-500">
                No hay usuarios.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Paginación --}}
  <div>
    @if(isset($usuarios) && method_exists($usuarios,'links'))
      {{ $usuarios->links() }}
    @endif
  </div>

</div>
@endsection

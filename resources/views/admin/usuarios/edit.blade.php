@extends('layouts.base')
@section('title','Editar Usuario')

@section('content')
@php
  $roleSel = $roleSel ?? ($user->roles->first()?->name ?? '');

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

  $iconUser = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M15 7.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm-10.5 13.5a7.5 7.5 0 0 1 15 0"/>
  </svg>';

  $iconMail = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M21.75 6.75v10.5A2.25 2.25 0 0 1 19.5 19.5H4.5A2.25 2.25 0 0 1 2.25 17.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5H4.5A2.25 2.25 0 0 0 2.25 6.75m19.5 0-9.75 6.75L2.25 6.75"/>
  </svg>';

  $iconLock = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M16.5 10.5V8.25a4.5 4.5 0 0 0-9 0V10.5"/>
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M6.75 10.5h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75v-6A2.25 2.25 0 0 1 6.75 10.5Z"/>
  </svg>';
@endphp

<div class="max-w-4xl mx-auto space-y-4">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Editar Usuario</h1>
      <p class="text-sm text-slate-500 mt-1">
        Actualiza datos, empresa y rol. (Contraseña opcional)
      </p>
    </div>

    {{-- ✅ [CAMBIO 1] Botón volver estilo Krayin (sin btn/btn-outline) --}}
    <a href="{{ route('admin.usuarios') }}"
       class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
              bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
      <span class="h-4 w-4">{!! $iconBack !!}</span>
      Volver
    </a>
  </div>

  {{-- Errors --}}
  @if ($errors->any())
    {{-- ✅ [CAMBIO 2] Alert component --}}
    <x-ui.alert type="err">
      ❌ {{ $errors->first() }}
    </x-ui.alert>
  @endif

  {{-- Form --}}
  <x-ui.card>
    <form method="POST" action="{{ route('admin.usuarios.update',$user) }}" class="space-y-5">
      @csrf
      @method('PUT')

      {{-- ✅ [CAMBIO 3] Grid Tailwind (sin col-6 / col-12) --}}
      <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

        <div class="md:col-span-6">
          <x-ui.input
            label="Nombre"
            name="name"
            required
            :value="old('name', $user->name)"
            placeholder="Nombre completo"
            :icon="$iconUser"
          />
        </div>

        <div class="md:col-span-6">
          <x-ui.input
            label="Correo"
            type="email"
            name="email"
            required
            :value="old('email', $user->email)"
            placeholder="correo@dominio.com"
            :icon="$iconMail"
          />
        </div>

        {{-- Empresa (solo SuperAdmin) --}}
        @if(!empty($isSuperAdmin))
          <div class="md:col-span-6">
            {{-- ✅ [CAMBIO 4] Select component (sin select-wrap) --}}
            <x-ui.select name="empresa_id" label="Empresa">
              <option value="">— Sin empresa —</option>
              @foreach($empresas as $e)
                <option value="{{ $e->id }}" @selected(old('empresa_id',$user->empresa_id)==$e->id)>
                  {{ $e->nombre }}
                </option>
              @endforeach
            </x-ui.select>
          </div>
        @endif

        <div class="md:col-span-6">
          <x-ui.select name="role" label="Rol">
            <option value="">— Sin rol —</option>
            @foreach($roles as $r)
              <option value="{{ $r->name }}" @selected(old('role',$roleSel)===$r->name)>
                {{ $r->name }}
              </option>
            @endforeach
          </x-ui.select>
        </div>

        <div class="md:col-span-6">
          {{-- ✅ [CAMBIO 5] Checkbox bonito (sin inline style) --}}
          <label class="flex items-center gap-3 rounded-xl border border-slate-900/10 bg-white px-4 h-11 cursor-pointer hover:bg-slate-50">
            <input
              type="checkbox"
              name="activo"
              value="1"
              class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200"
              {{ old('activo',$user->activo) ? 'checked' : '' }}
            >
            <span class="text-sm font-extrabold text-slate-800">Activo</span>
          </label>
        </div>

        <div class="md:col-span-12">
          <x-ui.input
            label="Nueva contraseña (opcional)"
            type="password"
            name="password"
            placeholder="Dejar vacío para no cambiar"
            :icon="$iconLock"
          />
          <p class="text-xs text-slate-500 mt-2">
            Si dejas este campo vacío, la contraseña no se modificará.
          </p>
        </div>

      </div>

      {{-- ✅ [CAMBIO 6] Footer acciones consistente --}}
      <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('admin.usuarios') }}"
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
@endsection

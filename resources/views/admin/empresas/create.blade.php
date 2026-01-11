@extends('layouts.base')
@section('title','Nueva Empresa')

@section('content')
@php
  // Iconos (Heroicons inline)
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

  $iconUpload = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M12 16.5V3m0 0 4.5 4.5M12 3 7.5 7.5"/>
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M3.75 15.75v3A2.25 2.25 0 0 0 6 21h12a2.25 2.25 0 0 0 2.25-2.25v-3"/>
  </svg>';

  $iconUser = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z"/>
    <path stroke-linecap="round" stroke-linejoin="round"
      d="M4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z"/>
  </svg>';
@endphp

<div class="max-w-5xl mx-auto space-y-4">

  {{-- Header --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div>
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Nueva Empresa</h1>
      <p class="text-sm text-slate-500 mt-1">Registro de empresa (solo SuperAdmin).</p>
    </div>

    <a href="{{ route('admin.empresas') }}"
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
    <form method="POST" action="{{ route('admin.empresas.store') }}" enctype="multipart/form-data" class="space-y-5">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

        {{-- Nombre --}}
        <div class="md:col-span-6">
          <x-ui.input
            label="Nombre"
            name="nombre"
            required
            :value="old('nombre')"
            placeholder="Ej: Constructora Los Robles, S.A."
          />
        </div>

        {{-- Admin asignado --}}
        <div class="md:col-span-6">
          <div class="space-y-1">
            <label class="text-sm font-extrabold text-slate-900">Administrador asignado</label>

            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                {!! $iconUser !!}
              </span>

              <select
                name="admin_user_id"
                class="w-full h-11 rounded-xl border border-slate-900/10 bg-white pl-10 pr-3
                       text-sm font-semibold text-slate-900 shadow-sm
                       focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300"
              >
                <option value="">— Sin asignar por ahora —</option>
                @foreach($usuarios as $u)
                  <option value="{{ $u->id }}" @selected(old('admin_user_id')==$u->id)>
                    {{ $u->name }} ({{ $u->email }}) {{ $u->empresa_id ? '• ya tiene empresa' : '' }}
                  </option>
                @endforeach
              </select>
            </div>

            <p class="text-xs font-semibold text-slate-500">
              Si seleccionas un usuario, automáticamente se le asignará <b>empresa_id</b>.
            </p>
          </div>
        </div>

        {{-- RUC --}}
        <div class="md:col-span-4">
          <x-ui.input label="RUC" name="ruc" :value="old('ruc')" placeholder="Ej: 1556789-1-123456" />
        </div>

        {{-- DV --}}
        <div class="md:col-span-2">
          <x-ui.input label="DV" name="dv" :value="old('dv')" placeholder="Ej: 34" />
        </div>

        {{-- Contacto --}}
        <div class="md:col-span-6">
          <x-ui.input label="Contacto" name="contacto" :value="old('contacto')" placeholder="Ej: Juan Pérez" />
        </div>

        {{-- Teléfono --}}
        <div class="md:col-span-6">
          <x-ui.input label="Teléfono" name="telefono" :value="old('telefono')" placeholder="Ej: 6000-0000" />
        </div>

        {{-- Email --}}
        <div class="md:col-span-6">
          <x-ui.input label="Email" name="email" type="email" :value="old('email')" placeholder="contacto@empresa.com" />
        </div>

        {{-- Dirección --}}
        <div class="md:col-span-12">
          <x-ui.input label="Dirección" name="direccion" :value="old('direccion')" placeholder="Dirección completa..." />
        </div>

        {{-- Logo --}}
        <div class="md:col-span-12">
          <div class="space-y-1">
            <label class="text-sm font-extrabold text-slate-900">Logo de la empresa</label>

            <label class="group flex items-center justify-between gap-3 rounded-2xl border border-dashed border-slate-900/15 bg-slate-50 px-4 py-3 hover:bg-slate-100 cursor-pointer">
              <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center h-10 w-10 rounded-2xl bg-white border border-slate-900/10 shadow-sm text-slate-500 group-hover:text-slate-700">
                  {!! $iconUpload !!}
                </span>
                <div class="min-w-0">
                  <div class="font-extrabold text-slate-900">Subir logo</div>
                  <div class="text-xs font-semibold text-slate-500">PNG/JPG recomendado (300x300)</div>
                </div>
              </div>

              <span class="text-xs font-extrabold text-slate-500">Seleccionar</span>
              <input class="hidden" type="file" name="logo" accept="image/*">
            </label>
          </div>
        </div>

        {{-- Activa --}}
        <div class="md:col-span-6">
          <div class="rounded-2xl border border-slate-900/10 bg-white px-4 py-3 shadow-sm">
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                type="checkbox"
                name="activa"
                value="1"
                class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200"
                {{ old('activa',1) ? 'checked' : '' }}
              >
              <div class="min-w-0">
                <div class="font-extrabold text-slate-900">Empresa activa</div>
                <div class="text-xs font-semibold text-slate-500">Visible y operativa en el sistema.</div>
              </div>
            </label>
          </div>
        </div>

        {{-- Activo (flag adicional) --}}
        <div class="md:col-span-6">
          <div class="rounded-2xl border border-slate-900/10 bg-white px-4 py-3 shadow-sm">
            <label class="flex items-center gap-3 cursor-pointer">
              <input
                type="checkbox"
                name="activo"
                value="1"
                class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-200"
                {{ old('activo',1) ? 'checked' : '' }}
              >
              <div class="min-w-0">
                <div class="font-extrabold text-slate-900">Activo (flag adicional)</div>
                <div class="text-xs font-semibold text-slate-500">Bandera extra para validaciones internas.</div>
              </div>
            </label>
          </div>
        </div>

      </div>

      {{-- Footer actions --}}
      <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('admin.empresas') }}"
           class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                  bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm">
          Cancelar
        </a>

        <button type="submit"
                class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-semibold
                       bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm">
          <span class="h-4 w-4">{!! $iconSave !!}</span>
          Guardar Empresa
        </button>
      </div>
    </form>
  </x-ui.card>

</div>
@endsection

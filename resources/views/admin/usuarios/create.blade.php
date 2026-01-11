@extends('layouts.base')
@section('title','Nuevo Usuario')

@section('content')
@php
  // Si el controller no manda la variable, asumimos false
  $isSuperAdmin = $isSuperAdmin ?? false;

  // Activo 0/1 consistente, respeta old()
  $activo = (int) old('activo', 1);

  // ICONOS "Kraya" (inline)
  $icon = function($name){
    if($name==='back') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='save') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2"/><path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2"/><path d="M7 3v5h8" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='x') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';

    if($name==='user') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" stroke="currentColor" stroke-width="2"/><path d="M4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
    if($name==='mail') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3.75 7.5A2.25 2.25 0 0 1 6 5.25h12A2.25 2.25 0 0 1 20.25 7.5v9A2.25 2.25 0 0 1 18 18.75H6A2.25 2.25 0 0 1 3.75 16.5v-9Z" stroke="currentColor" stroke-width="2"/><path d="m4.5 7.5 7.2 5.4a1.5 1.5 0 0 0 1.8 0l7.2-5.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if($name==='lock') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M7.5 10.5V8.25a4.5 4.5 0 0 1 9 0v2.25" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6.75 10.5h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75v-6A2.25 2.25 0 0 1 6.75 10.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
    if($name==='building') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3.75 21h16.5M6 21V4.5A2.25 2.25 0 0 1 8.25 2.25h7.5A2.25 2.25 0 0 1 18 4.5V21M9 6.75h.008V6.758H9V6.75Zm0 3h.008V9.758H9V9.75Zm0 3h.008v.008H9v-.008Zm6-6h.008V6.758H15V6.75Zm0 3h.008V9.758H15V9.75Zm0 3h.008v.008H15v-.008Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
    if($name==='roles') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2.25l7.5 4.5v6.75c0 4.477-3.248 8.385-7.5 9.75-4.252-1.365-7.5-5.273-7.5-9.75V6.75L12 2.25Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9 12.75 11.25 15 15 9.75" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    return '';
  };
@endphp

<div class="max-w-4xl mx-auto space-y-4">

  {{-- HEADER --}}
  <div class="flex flex-wrap items-start justify-between gap-3">
    <div class="min-w-0">
      <h1 class="text-xl font-extrabold tracking-tight text-slate-900">Nuevo Usuario</h1>
      <p class="text-sm text-slate-500 mt-1">
        Crea el usuario, asigna rol y estado.
        @if($isSuperAdmin)
          <span class="font-semibold">Como Super Admin también puedes asignar empresa.</span>
        @endif
      </p>
    </div>

    <a
      class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
             bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
      href="{{ route('admin.usuarios') }}"
    >
      {!! $icon('back') !!} Volver
    </a>
  </div>

  {{-- ERRORES --}}
  @if ($errors->any())
    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      <div class="flex items-center gap-2 font-extrabold">
        <span>{!! $icon('alert') !!}</span>
        <span>Hay errores</span>
      </div>
      <ul class="list-disc ml-6 mt-2 text-sm font-semibold">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- CARD --}}
  <div class="rounded-2xl border border-slate-900/10 bg-white shadow-sm overflow-hidden">

    <div class="px-5 py-4 border-b border-slate-900/10 bg-slate-50">
      <div class="text-sm font-extrabold text-slate-900">Datos del usuario</div>
      <div class="text-xs font-semibold text-slate-500">Completa los campos y guarda.</div>
    </div>

    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="p-5">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Nombre --}}
        <div>
          <label class="text-xs font-extrabold text-slate-600">Nombre</label>
          <div class="mt-2 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              {!! $icon('user') !!}
            </span>
            <input
              class="w-full h-11 rounded-xl border border-slate-900/10 pl-10 pr-3 text-sm font-semibold
                     focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
              name="name"
              value="{{ old('name') }}"
              required
            >
          </div>
        </div>

        {{-- Correo --}}
        <div>
          <label class="text-xs font-extrabold text-slate-600">Correo</label>
          <div class="mt-2 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              {!! $icon('mail') !!}
            </span>
            <input
              class="w-full h-11 rounded-xl border border-slate-900/10 pl-10 pr-3 text-sm font-semibold
                     focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
              type="email"
              name="email"
              value="{{ old('email') }}"
              required
            >
          </div>
        </div>

        {{-- Empresa (solo SuperAdmin) --}}
        @if($isSuperAdmin)
          <div>
            <label class="text-xs font-extrabold text-slate-600">Empresa</label>
            <div class="mt-2 relative">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                {!! $icon('building') !!}
              </span>
              <select
                name="empresa_id"
                class="w-full h-11 rounded-xl border border-slate-900/10 pl-10 pr-3 text-sm font-semibold bg-white
                       focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
              >
                <option value="">— Sin empresa —</option>
                @foreach($empresas as $e)
                  <option value="{{ $e->id }}" @selected((string)old('empresa_id') === (string)$e->id)>
                    {{ $e->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="mt-1 text-[11px] font-semibold text-slate-500">
              Si dejas “Sin empresa”, ese usuario no podrá usar módulos que exigen empresa.
            </div>
          </div>
        @endif

        {{-- Rol --}}
        <div class="{{ $isSuperAdmin ? '' : 'md:col-span-2' }}">
          <label class="text-xs font-extrabold text-slate-600">Rol</label>
          <div class="mt-2 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              {!! $icon('roles') !!}
            </span>
            <select
              name="role"
              class="w-full h-11 rounded-xl border border-slate-900/10 pl-10 pr-3 text-sm font-semibold bg-white
                     focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
            >
              <option value="">— Sin rol —</option>
              @foreach($roles as $r)
                <option value="{{ $r->name }}" @selected((string)old('role') === (string)$r->name)>
                  {{ $r->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="mt-1 text-[11px] font-semibold text-slate-500">
            El rol define permisos y visibilidad del menú.
          </div>
        </div>

        {{-- Contraseña --}}
        <div class="md:col-span-2">
          <label class="text-xs font-extrabold text-slate-600">Contraseña</label>
          <div class="mt-2 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
              {!! $icon('lock') !!}
            </span>
            <input
              class="w-full h-11 rounded-xl border border-slate-900/10 pl-10 pr-3 text-sm font-semibold
                     focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 outline-none"
              type="password"
              name="password"
              placeholder="Mínimo 8 caracteres"
              required
            >
          </div>
        </div>

        {{-- Switch Activo (SIEMPRE ENVÍA 0/1) --}}
        <div class="md:col-span-2">
          <label class="flex items-center justify-between gap-3 rounded-2xl border border-slate-900/10 bg-white px-4 py-3">
            <div class="min-w-0">
              <div class="text-sm font-extrabold text-slate-900">Activo</div>
              <div class="text-xs font-semibold text-slate-500">
                Si lo desactivas, el usuario no debería poder acceder.
              </div>
            </div>

            <span class="flex items-center gap-3 shrink-0">
              <span class="text-xs font-extrabold {{ $activo ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $activo ? 'Activo' : 'Inactivo' }}
              </span>

              <span class="relative inline-flex items-center">
                {{-- hidden => siempre manda 0 si está apagado --}}
                <input type="hidden" name="activo" value="0">

                <input
                  type="checkbox"
                  name="activo"
                  value="1"
                  class="peer sr-only"
                  {{ $activo ? 'checked' : '' }}
                >
                <span class="h-7 w-12 rounded-full bg-slate-200 peer-checked:bg-emerald-500 transition"></span>
                <span class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow-sm transition
                             peer-checked:translate-x-5"></span>
              </span>
            </span>
          </label>
        </div>

      </div>

      {{-- ACCIONES --}}
      <div class="mt-6 flex flex-wrap items-center justify-between gap-2">
        <a
          class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                 bg-white border border-slate-900/10 hover:border-slate-900/20 shadow-sm"
          href="{{ route('admin.usuarios') }}"
        >
          {!! $icon('x') !!} Cancelar
        </a>

        <button
          class="inline-flex items-center gap-2 rounded-xl px-4 h-11 text-sm font-extrabold
                 bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm"
          type="submit"
        >
          {!! $icon('save') !!} Guardar usuario
        </button>
      </div>

    </form>
  </div>
</div>
@endsection

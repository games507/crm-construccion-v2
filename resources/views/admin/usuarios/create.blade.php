@extends('layouts.base')
@section('title','Nuevo Usuario')

@section('content')
@php
  $isSuperAdmin = $isSuperAdmin ?? false;
  $activo = (int) old('activo', 1);

  $empresasStats = collect($empresas ?? [])->map(function($e){
    $limite = (int)($e->usuarios_limite ?? 0);
    $usados = \App\Models\User::where('empresa_id', $e->id)->count();

    return [
      'id' => $e->id,
      'nombre' => $e->nombre,
      'limite' => $limite,
      'usados' => $usados,
      'licencia_estado' => $e->licencia_estado ?? 'activa',
      'llena' => $limite > 0 && $usados >= $limite,
    ];
  });

  $icon = function($name){
    if($name==='back') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='save') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" stroke="currentColor" stroke-width="2"/><path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2"/><path d="M7 3v5h8" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='x') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='alert') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" stroke="currentColor" stroke-width="2"/><path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M12 17h.01" stroke="currentColor" stroke-width="3" stroke-linecap="round"/></svg>';
    if($name==='user') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" stroke="currentColor" stroke-width="2"/><path d="M4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
    if($name==='mail') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3.75 7.5A2.25 2.25 0 0 1 6 5.25h12A2.25 2.25 0 0 1 20.25 7.5v9A2.25 2.25 0 0 1 18 18.75H6A2.25 2.25 0 0 1 3.75 16.5v-9Z" stroke="currentColor" stroke-width="2"/><path d="m4.5 7.5 7.2 5.4a1.5 1.5 0 0 0 1.8 0l7.2-5.4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if($name==='lock') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M7.5 10.5V8.25a4.5 4.5 0 0 1 9 0v2.25" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M6.75 10.5h10.5A2.25 2.25 0 0 1 19.5 12.75v6A2.25 2.25 0 0 1 17.25 21H6.75A2.25 2.25 0 0 1 4.5 18.75v-6A2.25 2.25 0 0 1 6.75 10.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/></svg>';
    if($name==='building') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3.75 21h16.5M6 21V4.5A2.25 2.25 0 0 1 8.25 2.25h7.5A2.25 2.25 0 0 1 18 4.5V21" stroke="currentColor" stroke-width="2"/><path d="M9 7h.01M9 11h.01M9 15h.01M15 7h.01M15 11h.01M15 15h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='roles') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2.25l7.5 4.5v6.75c0 4.477-3.248 8.385-7.5 9.75-4.252-1.365-7.5-5.273-7.5-9.75V6.75L12 2.25Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/><path d="M9 12.75 11.25 15 15 9.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    return '';
  };
@endphp

<style>
.vs-wrap{max-width:1180px;margin:0 auto;padding:18px}
.vs-hero{position:relative;overflow:hidden;border-radius:30px;padding:24px;background:radial-gradient(circle at 12% 15%,rgba(34,211,238,.28),transparent 30%),radial-gradient(circle at 88% 18%,rgba(59,130,246,.28),transparent 32%),linear-gradient(135deg,#07172e,#0b2f54,#061425);color:white;box-shadow:0 24px 70px rgba(2,6,23,.22);margin-bottom:18px}
.vs-hero-inner{display:flex;align-items:center;justify-content:space-between;gap:28px;flex-wrap:wrap;position:relative;z-index:1}
.vs-hero-icon{height:82px;width:82px;border-radius:24px;background:white;color:#0b4f7d;display:flex;align-items:center;justify-content:center;box-shadow:0 14px 34px rgba(0,0,0,.18);flex-shrink:0}
.vs-hero-title{font-size:28px;font-weight:950;line-height:1.1}
.vs-hero-sub{margin-top:7px;color:#cbd5e1;font-weight:700;font-size:13px;max-width:680px}

.kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}
@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.kpi-grid{grid-template-columns:1fr}}

.kpi{background:white;border-radius:24px;border:1px solid #e2e8f0;padding:16px;box-shadow:0 14px 40px rgba(15,23,42,.06)}
.kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.kpi-value{margin-top:8px;font-size:22px;font-weight:950;color:#0f172a}

.vs-card{background:white;border-radius:28px;border:1px solid #e2e8f0;box-shadow:0 18px 50px rgba(15,23,42,.07);overflow:hidden}
.vs-card-head{padding:18px;border-bottom:1px solid #e2e8f0;background:#f8fafc}
.vs-card-title{font-weight:950;color:#0f172a;font-size:16px}
.vs-card-sub{font-size:12px;color:#64748b;margin-top:4px;font-weight:700}
.vs-card-body{padding:22px}

.label-vs{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.input-vs,.select-vs{width:100%;height:42px;border:1px solid #dbe2ea;border-radius:16px;padding:0 14px;font-weight:700;outline:none;background:white;color:#0f172a}
.input-vs-icon,.select-vs-icon{padding-left:42px}
.input-vs:focus,.select-vs:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(14,165,233,.12)}

.vs-btn{height:40px;border:none;border-radius:16px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;gap:10px;font-weight:900;text-decoration:none;cursor:pointer;transition:.2s ease}
.vs-btn:hover{transform:translateY(-2px)}
.vs-btn-primary{background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;box-shadow:0 12px 30px rgba(15,23,42,.15)}
.vs-btn-light{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}

.vs-switch-track{position:relative;width:52px;height:30px;border-radius:999px;background:#e2e8f0;border:1px solid #cbd5e1;transition:.25s ease;flex-shrink:0}
.vs-switch-track::after{content:"";position:absolute;top:3px;left:3px;width:22px;height:22px;border-radius:999px;background:white;box-shadow:0 7px 16px rgba(15,23,42,.20);transition:.25s ease}
.peer:checked ~ .vs-switch-track{background:linear-gradient(135deg,#0f172a,#0b4f7d);border-color:#0b4f7d;box-shadow:0 10px 22px rgba(11,79,125,.25)}
.peer:checked ~ .vs-switch-track::after{transform:translateX(22px)}
</style>

<div class="vs-wrap">

  <div class="flex justify-end mb-4">
    <a href="{{ route('admin.usuarios') }}" class="vs-btn vs-btn-light">
      {!! $icon('back') !!} Volver
    </a>
  </div>

  <div class="vs-hero">
    <div class="vs-hero-inner">
      <div class="flex items-center gap-6 flex-wrap">
        <div class="vs-hero-icon">
          {!! $icon('user') !!}
        </div>

        <div>
          <div class="text-[11px] uppercase tracking-[.18em] text-blue-200 font-black">VerticeSoft</div>
          <div class="vs-hero-title">Nuevo Usuario</div>
          <div class="vs-hero-sub">
            Gestiona accesos, roles, empresa asignada y control de licenciamiento SaaS.
          </div>
        </div>
      </div>

      <div class="text-right">
        <div class="text-[11px] uppercase tracking-[.18em] text-blue-200 font-black">Seguridad</div>
        <div class="mt-2 text-lg font-black">Control de acceso</div>
      </div>
    </div>
  </div>

  <div class="kpi-grid">
    <div class="kpi">
      <div class="kpi-label">Tipo</div>
      <div class="kpi-value">Usuario</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Estado inicial</div>
      <div class="kpi-value text-emerald-700">Activo</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Roles disponibles</div>
      <div class="kpi-value text-indigo-700">{{ count($roles ?? []) }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Empresas</div>
      <div class="kpi-value">{{ count($empresas ?? []) }}</div>
    </div>
  </div>

  @if ($errors->any())
    <div class="mb-4 rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-black">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="vs-card">
    <div class="vs-card-head">
      <div class="vs-card-title">Datos del usuario</div>
      <div class="vs-card-sub">Completa la información, asigna rol y empresa según la licencia disponible.</div>
    </div>

    <form method="POST" action="{{ route('admin.usuarios.store') }}" class="vs-card-body">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
          <label class="label-vs">Nombre</label>
          <div class="mt-2 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">{!! $icon('user') !!}</span>
            <input class="input-vs input-vs-icon" name="name" value="{{ old('name') }}" required>
          </div>
        </div>

        <div>
          <label class="label-vs">Correo</label>
          <div class="mt-2 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">{!! $icon('mail') !!}</span>
            <input class="input-vs input-vs-icon" type="email" name="email" value="{{ old('email') }}" required>
          </div>
        </div>

@if($isSuperAdmin)
  {{-- Selector empresa solo para SuperAdmin --}}
  <div>
    <label class="label-vs">Empresa</label>
    <div class="mt-2 relative">
      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">{!! $icon('building') !!}</span>
      <select name="empresa_id" class="select-vs select-vs-icon" required>
        <option value="">— Selecciona empresa —</option>
        @foreach($empresasStats as $e)
          <option value="{{ $e['id'] }}" @selected((string)old('empresa_id') === (string)$e['id'])>
            {{ $e['nombre'] }} — {{ $e['usados'] }} / {{ $e['limite'] ?: '∞' }} usuarios
          </option>
        @endforeach
      </select>
    </div>
  </div>
@else
  {{-- Empresa fija para AdminEmpresa --}}
  <div>
    <label class="label-vs">Empresa asignada</label>
    <div class="mt-2 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3">
      <div class="text-sm font-black text-blue-900">
        {{ $empresaActual->nombre ?? 'Empresa actual' }}
      </div>
      <div class="text-xs font-bold text-blue-700">
        El usuario será creado automáticamente en esta empresa.
      </div>
    </div>
  </div>
@endif

          <div class="mt-2 rounded-2xl border border-blue-200 bg-blue-50 p-3">
            <div class="text-xs font-black text-blue-700 uppercase">Licenciamiento</div>
            <div class="text-sm font-semibold text-blue-900">
              El sistema bloqueará el guardado si la empresa alcanzó su límite de usuarios.
            </div>
          </div>
        </div>

        <div>
          <label class="label-vs">Rol</label>
          <div class="mt-2 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">{!! $icon('roles') !!}</span>
            <select name="role" class="select-vs select-vs-icon">
              <option value="">— Sin rol —</option>
              @foreach($roles as $r)
                <option value="{{ $r->name }}" @selected((string)old('role') === (string)$r->name)>
                  {{ $r->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="md:col-span-2">
          <label class="label-vs">Contraseña</label>
          <div class="mt-2 relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">{!! $icon('lock') !!}</span>
            <input class="input-vs input-vs-icon" type="password" name="password" placeholder="Mínimo 8 caracteres" required>
          </div>
        </div>

        <div class="md:col-span-2">
          <label class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
            <div>
              <div class="text-sm font-black text-slate-800">Usuario activo</div>
              <div class="text-xs font-bold text-slate-500">Permite el acceso al sistema.</div>
            </div>

            <span class="relative inline-flex items-center">
              <input type="hidden" name="activo" value="0">
              <input type="checkbox" name="activo" value="1" class="peer sr-only" {{ $activo ? 'checked' : '' }}>
              <span class="vs-switch-track"></span>
            </span>
          </label>
        </div>

      </div>

      <div class="mt-6 flex flex-wrap items-center justify-between gap-2">
        <a class="vs-btn vs-btn-light" href="{{ route('admin.usuarios') }}">
          {!! $icon('x') !!} Cancelar
        </a>

        <button class="vs-btn vs-btn-primary" type="submit">
          {!! $icon('save') !!} Guardar usuario
        </button>
      </div>

    </form>
  </div>
</div>
@endsection
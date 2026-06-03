@extends('layouts.base')
@section('title','Editar Empresa')

@section('content')
@php
  $logo = $empresa->logo_path ?? $empresa->logo ?? null;
  $logoUrl = $logo ? asset('storage/' . ltrim($logo, '/')) : null;

  $correo = $empresa->email ?? $empresa->correo ?? null;
  $activa = (int)($empresa->activa ?? 1) === 1;

  $venceVal = old('licencia_vence',
    !empty($empresa->licencia_vence)
      ? \Illuminate\Support\Carbon::parse($empresa->licencia_vence)->format('Y-m-d')
      : ''
  );

  $planActual = old('plan', $empresa->plan ?? 'basico');
  $licenciaEstado = old('licencia_estado', $empresa->licencia_estado ?? 'activa');
  $usuariosLimite = old('usuarios_limite', $empresa->usuarios_limite ?? 5);

  $licenciaLabel = match($licenciaEstado) {
    'por_vencer' => 'Por vencer',
    'vencida' => 'Vencida',
    default => 'Activa',
  };

  $icon = function($name){
    if($name==='back') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    if($name==='save') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z" stroke="currentColor" stroke-width="2"/><path d="M17 21v-8H7v8" stroke="currentColor" stroke-width="2"/><path d="M7 3v5h8" stroke="currentColor" stroke-width="2"/></svg>';
    if($name==='building') return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 9h1M9 13h1M9 17h1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    if($name==='image') return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="2"/><path d="m3 16 5-5 4 4 3-3 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="8" cy="9" r="1.5" fill="currentColor"/></svg>';
    if($name==='upload') return '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 16V4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="m7 9 5-5 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M20 16v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
    return '';
  };
@endphp

<style>
.vs-wrap{max-width:1180px;margin:0 auto;padding:18px}
.vs-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.vs-title{font-size:30px;font-weight:950;color:#0f172a;line-height:1}
.vs-sub{margin-top:7px;font-size:13px;color:#64748b;font-weight:700}
.vs-actions{display:flex;gap:10px;flex-wrap:wrap}
.vs-btn{height:40px;border:none;border-radius:16px;padding:0 18px;display:inline-flex;align-items:center;justify-content:center;gap:10px;font-weight:900;text-decoration:none;cursor:pointer;transition:.2s ease}
.vs-btn:hover{transform:translateY(-2px)}
.vs-btn-primary{background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;box-shadow:0 12px 30px rgba(15,23,42,.15)}
.vs-btn-light{background:#f1f5f9;color:#334155;border:1px solid #e2e8f0}
.vs-hero{position:relative;overflow:hidden;border-radius:30px;padding:24px;background:radial-gradient(circle at 12% 15%, rgba(34,211,238,.28), transparent 30%),radial-gradient(circle at 88% 18%, rgba(59,130,246,.28), transparent 32%),linear-gradient(135deg,#07172e,#0b2f54,#061425);color:white;box-shadow:0 24px 70px rgba(2,6,23,.22);margin-bottom:18px}
.vs-hero-inner{display:flex;align-items:center;justify-content:space-between;gap:28px;flex-wrap:wrap;position:relative;z-index:1}
.vs-logo-box{height:82px;width:82px;border-radius:24px;background:white;display:flex;align-items:center;justify-content:center;overflow:hidden;box-shadow:0 14px 34px rgba(0,0,0,.18);flex-shrink:0}
.vs-logo-box img{width:100%;height:100%;object-fit:contain;padding:8px}
.vs-logo-empty{color:#0b4f7d}
.vs-hero-title{font-size:24px;font-weight:950;line-height:1.1}
.vs-hero-sub{margin-top:6px;color:#cbd5e1;font-weight:700;font-size:13px}
.kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}
@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:620px){.kpi-grid{grid-template-columns:1fr}}
.kpi{background:white;border-radius:24px;border:1px solid #e2e8f0;padding:16px;box-shadow:0 14px 40px rgba(15,23,42,.06)}
.kpi-label{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.kpi-value{margin-top:8px;font-size:22px;font-weight:950;color:#0f172a}
.vs-card{background:white;border-radius:28px;border:1px solid #e2e8f0;box-shadow:0 18px 50px rgba(15,23,42,.07);overflow:hidden}
.vs-card-head{padding:18px;border-bottom:1px solid #e2e8f0}
.vs-card-title{font-weight:950;color:#0f172a;font-size:16px}
.vs-card-sub{font-size:12px;color:#64748b;margin-top:4px;font-weight:700}
.vs-card-body{padding:22px}
.form-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:14px}
.col-12{grid-column:span 12}.col-6{grid-column:span 6}.col-4{grid-column:span 4}.col-3{grid-column:span 3}
@media(max-width:900px){.col-3,.col-4,.col-6{grid-column:span 12}}
.label-vs{font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b;font-weight:950}
.input-vs,.textarea-vs,.select-vs{width:100%;border:1px solid #dbe2ea;border-radius:16px;padding:0 14px;font-weight:700;outline:none;background:white;color:#0f172a}
.input-vs,.select-vs{height:42px}
.textarea-vs{padding-top:12px;padding-bottom:12px;min-height:110px;resize:vertical}
.input-vs:focus,.textarea-vs:focus,.select-vs:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(14,165,233,.12)}
.file-vs{width:100%;border:1px dashed #cbd5e1;border-radius:20px;padding:16px;background:#f8fafc}
.logo-preview-sm{height:56px;width:56px;border-radius:18px;background:white;border:1px solid #e2e8f0;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0}
.logo-preview-sm img{width:100%;height:100%;object-fit:contain;padding:5px}
.file-input-hidden{position:absolute;opacity:0;width:1px;height:1px;overflow:hidden}
.file-btn{height:42px;border-radius:16px;padding:0 16px;display:inline-flex;align-items:center;gap:9px;background:linear-gradient(135deg,#0f172a,#0b4f7d);color:white;font-size:13px;font-weight:950;cursor:pointer;box-shadow:0 12px 30px rgba(15,23,42,.13);transition:.2s ease}
.file-btn:hover{transform:translateY(-2px)}
.vs-switch{display:flex;align-items:center;justify-content:space-between;gap:14px;border:1px solid #e2e8f0;background:#f8fafc;border-radius:20px;padding:14px}
.vs-switch-track{position:relative;width:52px;height:30px;border-radius:999px;background:#e2e8f0;border:1px solid #cbd5e1;transition:.25s ease;flex-shrink:0}
.vs-switch-track::after{content:"";position:absolute;top:3px;left:3px;width:22px;height:22px;border-radius:999px;background:white;box-shadow:0 7px 16px rgba(15,23,42,.20);transition:.25s ease}
.vs-switch input:checked ~ .vs-switch-track{background:linear-gradient(135deg,#0f172a,#0b4f7d);border-color:#0b4f7d;box-shadow:0 10px 22px rgba(11,79,125,.25)}
.vs-switch input:checked ~ .vs-switch-track::after{transform:translateX(22px)}
</style>

<div class="vs-wrap">

  <div class="vs-head">
    <div>
      <div class="vs-title">Editar Empresa</div>
      <div class="vs-sub">Actualiza identidad, licencia SaaS, contacto y estado operativo.</div>
    </div>

    <div class="vs-actions">
      <a href="{{ route('admin.empresas') }}" class="vs-btn vs-btn-light">
        {!! $icon('back') !!} Volver
      </a>
    </div>
  </div>

  @if(session('ok'))
    <div class="mb-4 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 font-black">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-4 rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-black">
      {{ $errors->first() }}
    </div>
  @endif

  <div class="vs-hero">
    <div class="vs-hero-inner">
      <div class="flex items-center gap-6 flex-wrap">
        <div class="vs-logo-box">
          @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="Logo empresa">
          @else
            <div class="vs-logo-empty">{!! $icon('building') !!}</div>
          @endif
        </div>

        <div>
          <div class="vs-hero-title">{{ $empresa->nombre ?? 'Empresa' }}</div>
          <div class="vs-hero-sub">Plan: {{ ucfirst($planActual) }} · Licencia: {{ $licenciaLabel }}</div>
          <div class="vs-hero-sub">Vence: {{ $venceVal ?: 'Sin fecha' }} · Usuarios: {{ $usuariosLimite }}</div>
        </div>
      </div>

      <div class="text-right">
        <div class="text-[11px] uppercase tracking-[.18em] text-blue-200 font-black">Estado</div>
        <div class="mt-2 text-lg font-black">{{ $activa ? 'Activa' : 'Inactiva' }}</div>
      </div>
    </div>
  </div>

  <div class="kpi-grid">
    <div class="kpi">
      <div class="kpi-label">Empresa</div>
      <div class="kpi-value">{{ $empresa->nombre ?? '—' }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Plan</div>
      <div class="kpi-value text-indigo-700">{{ ucfirst($planActual) }}</div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Licencia</div>
      <div class="kpi-value {{ $licenciaEstado === 'vencida' ? 'text-rose-700' : ($licenciaEstado === 'por_vencer' ? 'text-amber-700' : 'text-emerald-700') }}">
        {{ $licenciaLabel }}
      </div>
    </div>

    <div class="kpi">
      <div class="kpi-label">Usuarios límite</div>
      <div class="kpi-value">{{ $usuariosLimite }}</div>
    </div>
  </div>

  <div class="vs-card">
    <div class="vs-card-head">
      <div class="vs-card-title">Datos de la empresa</div>
      <div class="vs-card-sub">Estos datos se usan en reportes, operación multiempresa y control de licencias.</div>
    </div>

    <form method="POST"
          action="{{ route('admin.empresas.update', $empresa) }}"
          enctype="multipart/form-data"
          class="vs-card-body">
      @csrf
      @method('PUT')

      <div class="form-grid">

        <div class="col-6">
          <label class="label-vs">Nombre de la empresa</label>
          <input name="nombre" value="{{ old('nombre', $empresa->nombre ?? '') }}" class="input-vs mt-2" required>
        </div>

        @if(isset($usuarios))
          <div class="col-6">
            <label class="label-vs">Administrador asignado</label>
            <select name="admin_user_id" class="select-vs mt-2">
              <option value="">— Sin asignar —</option>
              @foreach($usuarios as $u)
                <option value="{{ $u->id }}" @selected((string)old('admin_user_id', $empresa->admin_user_id ?? '') === (string)$u->id)>
                  {{ $u->name ?? $u->nombre ?? 'Usuario' }} ({{ $u->email }})
                </option>
              @endforeach
            </select>
          </div>
        @endif

        <div class="col-4">
          <label class="label-vs">RUC</label>
          <input name="ruc" value="{{ old('ruc', $empresa->ruc ?? '') }}" class="input-vs mt-2">
        </div>

        <div class="col-3">
          <label class="label-vs">DV</label>
          <input name="dv" value="{{ old('dv', $empresa->dv ?? '') }}" class="input-vs mt-2">
        </div>

        <div class="col-5">
          <label class="label-vs">Contacto</label>
          <input name="contacto" value="{{ old('contacto', $empresa->contacto ?? '') }}" class="input-vs mt-2">
        </div>

        <div class="col-6">
          <label class="label-vs">Teléfono</label>
          <input name="telefono" value="{{ old('telefono', $empresa->telefono ?? '') }}" class="input-vs mt-2">
        </div>

        <div class="col-6">
          <label class="label-vs">Correo</label>
          <input name="email" value="{{ old('email', $empresa->email ?? $empresa->correo ?? '') }}" class="input-vs mt-2">
        </div>

        <div class="col-12">
          <label class="label-vs">Dirección</label>
          <textarea name="direccion" class="textarea-vs mt-2">{{ old('direccion', $empresa->direccion ?? '') }}</textarea>
        </div>

        <div class="col-3">
          <label class="label-vs">Plan</label>
          <select name="plan" class="select-vs mt-2">
            <option value="basico" @selected($planActual === 'basico')>Básico</option>
            <option value="profesional" @selected($planActual === 'profesional')>Profesional</option>
            <option value="enterprise" @selected($planActual === 'enterprise')>Enterprise</option>
          </select>
        </div>

        <div class="col-3">
          <label class="label-vs">Estado licencia</label>
          <select name="licencia_estado" class="select-vs mt-2">
            <option value="activa" @selected($licenciaEstado === 'activa')>Activa</option>
            <option value="por_vencer" @selected($licenciaEstado === 'por_vencer')>Por vencer</option>
            <option value="vencida" @selected($licenciaEstado === 'vencida')>Vencida</option>
          </select>
        </div>

        <div class="col-3">
          <label class="label-vs">Vence</label>
          <input type="date" name="licencia_vence" value="{{ $venceVal }}" class="input-vs mt-2">
        </div>

        <div class="col-3">
          <label class="label-vs">Límite usuarios</label>
          <input type="number" name="usuarios_limite" min="1" value="{{ $usuariosLimite }}" class="input-vs mt-2">
        </div>

        <div class="col-6">
          <label class="label-vs">Estado operativo</label>
          <label class="vs-switch mt-2">
            <input type="checkbox" name="activa" value="1" class="sr-only" @checked(old('activa', $activa))>
            <div>
              <div class="text-sm font-black text-slate-800">Empresa activa</div>
              <div class="text-xs font-bold text-slate-500">Permite operar dentro del sistema.</div>
            </div>
            <span class="vs-switch-track"></span>
          </label>
        </div>

        <div class="col-6">
          <label class="label-vs">Activo interno</label>
          <label class="vs-switch mt-2">
            <input type="checkbox" name="activo" value="1" class="sr-only" @checked(old('activo', $empresa->activo ?? 1))>
            <div>
              <div class="text-sm font-black text-slate-800">Flag adicional</div>
              <div class="text-xs font-bold text-slate-500">Bandera extra para validaciones internas.</div>
            </div>
            <span class="vs-switch-track"></span>
          </label>
        </div>

        <div class="col-12">
          <label class="label-vs">Logo corporativo</label>

          <div class="file-vs mt-2">
            <div class="flex items-center gap-4 flex-wrap">
              <div class="logo-preview-sm">
                @if($logoUrl)
                  <img src="{{ $logoUrl }}" alt="Logo actual">
                @else
                  <div class="text-slate-400">{!! $icon('image') !!}</div>
                @endif
              </div>

              <div class="flex-1 min-w-[240px]">
                <label for="logoInput" class="file-btn">
                  {!! $icon('upload') !!} Seleccionar imagen
                </label>

                <input id="logoInput" type="file" name="logo" accept="image/*" class="file-input-hidden">

                <div class="mt-2 text-xs font-bold text-slate-500">
                  Formatos recomendados: PNG o JPG. Ideal con fondo transparente.
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="mt-6 flex justify-end gap-2">
        <a href="{{ route('admin.empresas') }}" class="vs-btn vs-btn-light">Cancelar</a>

        <button type="submit" class="vs-btn vs-btn-primary">
          {!! $icon('save') !!} Guardar cambios
        </button>
      </div>

    </form>
  </div>

</div>
@endsection
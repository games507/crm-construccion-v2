@extends('layouts.base')
@section('title','Nuevo Usuario')

@section('content')
@php
  // Si el controller no manda la variable, asumimos false
  $isSuperAdmin = $isSuperAdmin ?? false;
@endphp

<div class="card" style="max-width:980px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 6px 0;">Nuevo Usuario</h2>
      <div style="color:#64748b;font-size:13px;">Crea usuario, asigna empresa y rol.</div>
    </div>
    <a class="btn btn-outline" href="{{ route('admin.usuarios') }}">← Volver</a>
  </div>

  @if ($errors->any())
    <div class="alert" style="margin-top:14px;">
      {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('admin.usuarios.store') }}" style="margin-top:16px;">
    @csrf

    <div class="grid">
      {{-- Nombre --}}
      <div class="col-6">
        <div class="field">
          <div class="label">Nombre</div>
          <div class="input-wrap">
            <div class="input-ico">N</div>
            <input class="input" name="name" value="{{ old('name') }}" required>
          </div>
        </div>
      </div>

      {{-- Correo --}}
      <div class="col-6">
        <div class="field">
          <div class="label">Correo</div>
          <div class="input-wrap">
            <div class="input-ico">@</div>
            <input class="input" type="email" name="email" value="{{ old('email') }}" required>
          </div>
        </div>
      </div>

      {{-- Empresa (solo SuperAdmin) --}}
      @if($isSuperAdmin)
        <div class="col-6">
          <div class="field">
            <div class="label">Empresa</div>
            <div class="select-wrap">
              <div class="select-icon">E</div>
              <select name="empresa_id">
                <option value="">— Sin empresa —</option>
                @foreach($empresas as $e)
                  <option value="{{ $e->id }}" @selected(old('empresa_id')==$e->id)>{{ $e->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      @endif

      {{-- Rol (select single) --}}
      <div class="{{ $isSuperAdmin ? 'col-6' : 'col-12' }}">
        <div class="field">
          <div class="label">Rol</div>
          <div class="select-wrap">
            <div class="select-icon">R</div>
            <select name="role">
              <option value="">— Sin rol —</option>
              @foreach($roles as $r)
                <option value="{{ $r->name }}" @selected(old('role')===$r->name)>{{ $r->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      {{-- Password --}}
      <div class="col-6">
        <div class="field">
          <div class="label">Contraseña</div>
          <div class="input-wrap">
            <div class="input-ico">🔒</div>
            <input class="input" type="password" name="password" placeholder="Mínimo 8 caracteres" required>
          </div>
        </div>
      </div>

      {{-- Activo --}}
      <div class="col-6">
        <div class="field">
          <div class="label">Estado</div>
          <div style="display:flex;align-items:center;gap:10px;height:44px;padding:0 14px;border-radius:12px;
                      border:1px solid rgba(15,23,42,.12);background:#fff;box-shadow:0 10px 24px rgba(2,6,23,.06);">
            <input type="checkbox" name="activo" value="1" {{ old('activo', 1) ? 'checked' : '' }}>
            <span style="font-weight:800;color:#0f172a;">Activo</span>
            <span style="color:#64748b;font-size:12px;">(desmarca para Inactivo)</span>
          </div>
        </div>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
      <a class="btn btn-outline" href="{{ route('admin.usuarios') }}">Cancelar</a>
      <button class="btn" type="submit">Guardar usuario</button>
    </div>
  </form>
</div>
@endsection

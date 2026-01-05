@extends('layouts.base')
@section('title','Nueva Empresa')

@section('content')
<div class="card" style="max-width:980px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 6px 0;">Nueva Empresa</h2>
      <div style="color:#64748b;font-size:13px;">Registro de empresa (solo SuperAdmin)</div>
    </div>
    <a class="btn btn-outline" href="{{ route('admin.empresas') }}">← Volver</a>
  </div>

  @if ($errors->any())
    <div class="alert" style="margin-top:14px;">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.empresas.store') }}" enctype="multipart/form-data" style="margin-top:16px;">
    @csrf

    <div class="grid">
      <div class="col-6">
        <div class="field">
          <div class="label">Nombre</div>
          <div class="input-wrap">
            <div class="input-ico">E</div>
            <input class="input" name="nombre" value="{{ old('nombre') }}" required>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Administrador asignado</div>
          <div class="select-wrap">
            <div class="select-icon">U</div>
            <select name="admin_user_id">
              <option value="">— Sin asignar por ahora —</option>
              @foreach($usuarios as $u)
                <option value="{{ $u->id }}" @selected(old('admin_user_id')==$u->id)>
                  {{ $u->name }} ({{ $u->email }}) {{ $u->empresa_id ? '• ya tiene empresa' : '' }}
                </option>
              @endforeach
            </select>
          </div>
          <div style="font-size:12px;color:#64748b;margin-top:6px;">
            Si seleccionas un usuario, automáticamente se le asignará <b>empresa_id</b>.
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">RUC</div>
          <input class="input" name="ruc" value="{{ old('ruc') }}">
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">DV</div>
          <input class="input" name="dv" value="{{ old('dv') }}">
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Contacto</div>
          <input class="input" name="contacto" value="{{ old('contacto') }}">
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Teléfono</div>
          <input class="input" name="telefono" value="{{ old('telefono') }}">
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Email</div>
          <input class="input" type="email" name="email" value="{{ old('email') }}">
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <div class="label">Dirección</div>
          <input class="input" name="direccion" value="{{ old('direccion') }}">
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <div class="label">Logo de la empresa</div>
          <input class="input" type="file" name="logo" accept="image/*">
          <div style="font-size:12px;color:#64748b;margin-top:4px;">
            PNG / JPG recomendado (300x300)
          </div>
        </div>
      </div>

      <div class="col-6">
        <label style="display:flex;align-items:center;gap:10px;font-weight:800;">
          <input type="checkbox" name="activa" value="1" {{ old('activa',1) ? 'checked' : '' }}>
          Empresa activa
        </label>
      </div>

      <div class="col-6">
        <label style="display:flex;align-items:center;gap:10px;font-weight:800;">
          <input type="checkbox" name="activo" value="1" {{ old('activo',1) ? 'checked' : '' }}>
          Activo (flag adicional)
        </label>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
      <a class="btn btn-outline" href="{{ route('admin.empresas') }}">Cancelar</a>
      <button class="btn">Guardar Empresa</button>
    </div>
  </form>
</div>
@endsection

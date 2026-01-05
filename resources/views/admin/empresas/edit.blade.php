@extends('layouts.base')
@section('title','Editar Empresa')

@section('content')
<div class="card" style="max-width:980px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 6px 0;">Editar Empresa</h2>
      <div style="color:#64748b;font-size:13px;">Actualiza datos, logo y administrador asignado.</div>
    </div>
    <a class="btn btn-outline" href="{{ route('admin.empresas') }}">← Volver</a>
  </div>

  @if(session('ok'))
    <div class="alert" style="margin-top:14px;border-color:rgba(34,197,94,.25);background:rgba(34,197,94,.06);color:#14532d;">
      {{ session('ok') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="alert" style="margin-top:14px;">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.empresas.update',$empresa) }}" enctype="multipart/form-data" style="margin-top:16px;">
    @csrf
    @method('PUT')

    <div class="grid">
      <div class="col-6">
        <div class="field">
          <div class="label">Nombre</div>
          <div class="input-wrap">
            <div class="input-ico">E</div>
            <input class="input" name="nombre" value="{{ old('nombre',$empresa->nombre) }}" required>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Administrador asignado</div>
          <div class="select-wrap">
            <div class="select-icon">U</div>
            <select name="admin_user_id">
              <option value="">— Sin asignar —</option>
              @foreach($usuarios as $u)
                <option value="{{ $u->id }}" @selected(old('admin_user_id',$empresa->admin_user_id)==$u->id)>
                  {{ $u->name }} ({{ $u->email }}) {{ $u->empresa_id ? '• ya tiene empresa' : '' }}
                </option>
              @endforeach
            </select>
          </div>
          <div style="font-size:12px;color:#64748b;margin-top:6px;">
            Asignar admin también le pondrá <b>empresa_id</b> a ese usuario.
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">RUC</div>
          <input class="input" name="ruc" value="{{ old('ruc',$empresa->ruc) }}">
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">DV</div>
          <input class="input" name="dv" value="{{ old('dv',$empresa->dv) }}">
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Contacto</div>
          <input class="input" name="contacto" value="{{ old('contacto',$empresa->contacto) }}">
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Teléfono</div>
          <input class="input" name="telefono" value="{{ old('telefono',$empresa->telefono) }}">
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Email</div>
          <input class="input" type="email" name="email" value="{{ old('email',$empresa->email) }}">
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <div class="label">Dirección</div>
          <input class="input" name="direccion" value="{{ old('direccion',$empresa->direccion) }}">
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <div class="label">Logo de la empresa</div>

          @if($empresa->logo_path)
            <div style="display:flex;align-items:center;gap:12px;margin:10px 0;">
              <img src="{{ asset('storage/'.$empresa->logo_path) }}" style="width:64px;height:64px;object-fit:cover;border-radius:12px;border:1px solid rgba(15,23,42,.12);">
              <label style="display:flex;align-items:center;gap:10px;font-weight:800;">
                <input type="checkbox" name="remove_logo" value="1">
                Quitar logo actual
              </label>
            </div>
          @endif

          <input class="input" type="file" name="logo" accept="image/*">
          <div style="font-size:12px;color:#64748b;margin-top:4px;">
            PNG / JPG recomendado (300x300)
          </div>
        </div>
      </div>

      <div class="col-6">
        <label style="display:flex;align-items:center;gap:10px;font-weight:800;">
          <input type="checkbox" name="activa" value="1" {{ old('activa',$empresa->activa) ? 'checked' : '' }}>
          Empresa activa
        </label>
      </div>

      <div class="col-6">
        <label style="display:flex;align-items:center;gap:10px;font-weight:800;">
          <input type="checkbox" name="activo" value="1" {{ old('activo',$empresa->activo) ? 'checked' : '' }}>
          Activo (flag adicional)
        </label>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
      <a class="btn btn-outline" href="{{ route('admin.empresas') }}">Cancelar</a>
      <button class="btn" type="submit">Guardar cambios</button>
    </div>
  </form>
</div>
@endsection

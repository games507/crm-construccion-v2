@extends('layouts.base')
@section('title','Editar Proyecto')

@section('content')
<div class="card" style="max-width:980px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 6px 0;">Editar Proyecto</h2>
      <div style="color:#64748b;font-size:13px;">Actualiza datos generales y estado del proyecto.</div>
    </div>
    <a class="btn btn-outline" href="{{ route('admin.proyectos') }}">← Volver</a>
  </div>

  @if ($errors->any())
    <div class="alert" style="margin-top:14px;">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.proyectos.update',$proyecto) }}" style="margin-top:16px;">
    @csrf
    @method('PUT')

    <div class="grid">

      @can('empresas.ver')
      <div class="col-12">
        <div class="field">
          <div class="label">Empresa</div>
          <div class="select-wrap">
            <div class="select-icon">E</div>
            <select name="empresa_id" required>
              <option value="">— Selecciona —</option>
              @foreach($empresas as $e)
                <option value="{{ $e->id }}" @selected(old('empresa_id',$proyecto->empresa_id)==$e->id)>{{ $e->nombre }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      @endcan

      <div class="col-6">
        <div class="field">
          <div class="label">Código</div>
          <div class="input-wrap">
            <div class="input-ico">#</div>
            <input class="input" name="codigo" value="{{ old('codigo',$proyecto->codigo) }}" placeholder="PR-001">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Estado</div>
          <div class="select-wrap">
            <div class="select-icon">S</div>
            @php $estadoSel = old('estado',$proyecto->estado); @endphp
            <select name="estado" required>
              @foreach(['Planificado','En Progreso','En Pausa','Finalizado','Cancelado'] as $st)
                <option value="{{ $st }}" @selected($estadoSel===$st)>{{ $st }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <div class="label">Nombre</div>
          <div class="input-wrap">
            <div class="input-ico">N</div>
            <input class="input" name="nombre" value="{{ old('nombre',$proyecto->nombre) }}" required>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <div class="label">Ubicación</div>
          <div class="input-wrap">
            <div class="input-ico">U</div>
            <input class="input" name="ubicacion" value="{{ old('ubicacion',$proyecto->ubicacion) }}">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Fecha inicio</div>
          <div class="input-wrap">
            <div class="input-ico">I</div>
            <input class="input" type="date" name="fecha_inicio" value="{{ old('fecha_inicio', optional($proyecto->fecha_inicio)->format('Y-m-d')) }}">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Fecha fin</div>
          <div class="input-wrap">
            <div class="input-ico">F</div>
            <input class="input" type="date" name="fecha_fin" value="{{ old('fecha_fin', optional($proyecto->fecha_fin)->format('Y-m-d')) }}">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Presupuesto</div>
          <div class="input-wrap">
            <div class="input-ico">$</div>
            <input class="input" type="number" step="0.01" name="presupuesto" value="{{ old('presupuesto',$proyecto->presupuesto) }}">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Activo</div>
          <label style="display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid rgba(15,23,42,.12);border-radius:12px;background:#fff;box-shadow:0 10px 24px rgba(2,6,23,.06);height:44px;">
            <input type="checkbox" name="activo" value="1" {{ old('activo',$proyecto->activo) ? 'checked' : '' }}>
            <span style="font-weight:800;">{{ old('activo',$proyecto->activo) ? 'Activo' : 'Inactivo' }}</span>
          </label>
        </div>
      </div>

    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
      <a class="btn btn-outline" href="{{ route('admin.proyectos') }}">Cancelar</a>
      <button class="btn" type="submit">Guardar cambios</button>
    </div>
  </form>
</div>
@endsection

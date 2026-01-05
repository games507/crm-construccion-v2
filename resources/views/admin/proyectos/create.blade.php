@extends('layouts.base')
@section('title','Nuevo Proyecto')

@section('content')
<div class="card" style="max-width:980px;margin:0 auto;">

  <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
      <h2 style="margin:0 0 6px 0;">Nuevo Proyecto</h2>
      <div style="color:#64748b;font-size:13px;">Crea un proyecto y asígnalo a una empresa.</div>
    </div>
    <a class="btn btn-outline" href="{{ route('admin.proyectos') }}">← Volver</a>
  </div>

  @if ($errors->any())
    <div class="alert" style="margin-top:14px;">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.proyectos.store') }}" style="margin-top:16px;">
    @csrf

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
                <option value="{{ $e->id }}" @selected(old('empresa_id')==$e->id)>{{ $e->nombre }}</option>
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
            <input class="input" name="codigo" value="{{ old('codigo') }}" placeholder="PR-001">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Estado</div>
          <div class="select-wrap">
            <div class="select-icon">S</div>
            @php $estadoSel = old('estado','Planificado'); @endphp
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
            <input class="input" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej: PISO 200">
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="field">
          <div class="label">Ubicación</div>
          <div class="input-wrap">
            <div class="input-ico">U</div>
            <input class="input" name="ubicacion" value="{{ old('ubicacion') }}" placeholder="Ej: Edificio A, Torre ...">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Fecha inicio</div>
          <div class="input-wrap">
            <div class="input-ico">I</div>
            <input class="input" type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Fecha fin</div>
          <div class="input-wrap">
            <div class="input-ico">F</div>
            <input class="input" type="date" name="fecha_fin" value="{{ old('fecha_fin') }}">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Presupuesto</div>
          <div class="input-wrap">
            <div class="input-ico">$</div>
            <input class="input" type="number" step="0.01" name="presupuesto" value="{{ old('presupuesto') }}" placeholder="0.00">
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="field">
          <div class="label">Activo</div>
          <label style="display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid rgba(15,23,42,.12);border-radius:12px;background:#fff;box-shadow:0 10px 24px rgba(2,6,23,.06);height:44px;">
            <input type="checkbox" name="activo" value="1" {{ old('activo',1) ? 'checked' : '' }}>
            <span style="font-weight:700;">Sí</span>
            <span style="color:#64748b;font-size:12px;">(desmarca para inactivo)</span>
          </label>
        </div>
      </div>

    </div>

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:18px;">
      <a class="btn btn-outline" href="{{ route('admin.proyectos') }}">Cancelar</a>
      <button class="btn" type="submit">Guardar</button>
    </div>
  </form>
</div>
@endsection

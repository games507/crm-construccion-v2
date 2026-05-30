@extends('layouts.base')
@section('title','Editar Proyecto')

@section('content')
<style>
.vs-wrap{max-width:980px;margin:0 auto;padding:18px}
.vs-head{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.vs-title{font-size:30px;font-weight:950;color:#0f172a;line-height:1}
.vs-sub{margin-top:7px;font-size:13px;color:#64748b;font-weight:700}

.vs-btn{
height:40px;
border:none;
border-radius:16px;
padding:0 18px;
display:inline-flex;
align-items:center;
justify-content:center;
gap:10px;
font-weight:900;
text-decoration:none;
cursor:pointer;
transition:.2s ease
}

.vs-btn:hover{transform:translateY(-2px)}

.vs-btn-primary{
background:linear-gradient(135deg,#0f172a,#0b4f7d);
color:white;
box-shadow:0 12px 30px rgba(15,23,42,.15)
}

.vs-btn-primary:hover{color:white}

.vs-btn-light{
background:#f1f5f9;
color:#334155;
border:1px solid #e2e8f0
}

.vs-btn-light:hover{
background:#e2e8f0;
color:#334155
}

.vs-btn-danger{
background:#dc2626;
color:white;
box-shadow:0 12px 30px rgba(220,38,38,.18)
}

.vs-btn-danger:hover{color:white}

.vs-card{
background:white;
border-radius:28px;
border:1px solid #e2e8f0;
box-shadow:0 18px 50px rgba(15,23,42,.07);
overflow:hidden
}

.vs-card-head{
padding:18px;
border-bottom:1px solid #e2e8f0
}

.vs-card-title{
font-weight:950;
color:#0f172a;
font-size:16px
}

.vs-card-sub{
font-size:12px;
color:#64748b;
margin-top:4px;
font-weight:700
}

.vs-card-body{padding:22px}

.input-vs,.select-vs,.textarea-vs{
width:100%;
border:1px solid #dbe2ea;
border-radius:16px;
padding:0 14px;
font-weight:700;
outline:none;
background:white;
color:#0f172a;
}

.input-vs,.select-vs{
height:42px
}

.textarea-vs{
padding-top:12px;
padding-bottom:12px;
min-height:110px;
resize:vertical
}

.input-vs:focus,
.select-vs:focus,
.textarea-vs:focus{
border-color:#38bdf8;
box-shadow:0 0 0 4px rgba(14,165,233,.12)
}

.label-vs{
font-size:11px;
text-transform:uppercase;
letter-spacing:.08em;
color:#64748b;
font-weight:950
}

.form-grid{
display:grid;
grid-template-columns:repeat(12,1fr);
gap:14px
}

.col-12{grid-column:span 12}
.col-6{grid-column:span 6}
.col-4{grid-column:span 4}

@media(max-width:760px){
.col-6,.col-4{grid-column:span 12}
}

.badge-vs{
display:inline-flex;
align-items:center;
gap:8px;
padding:8px 14px;
border-radius:999px;
font-size:12px;
font-weight:900;
border:1px solid transparent
}

.badge-planeado{
background:#f1f5f9;
color:#334155;
border-color:#e2e8f0
}

.badge-ejecucion{
background:#eef2ff;
color:#4338ca;
border-color:#c7d2fe
}

.badge-pausado{
background:#fef3c7;
color:#92400e;
border-color:#fde68a
}

.badge-finalizado{
background:#dcfce7;
color:#166534;
border-color:#bbf7d0
}
</style>

@php
  $estadoActual = (string)($proyecto->estado ?? 'planeado');

  $badgeEstado = match($estadoActual){
    'en_ejecucion' => 'badge-ejecucion',
    'pausado'      => 'badge-pausado',
    'finalizado'   => 'badge-finalizado',
    default        => 'badge-planeado',
  };

  $estadoLabel = ucfirst(str_replace('_',' ',$estadoActual));
@endphp

<div class="vs-wrap">

  <div class="vs-head">

    <div>
      <div class="vs-title">Editar Proyecto</div>
      <div class="vs-sub">
        Actualiza información, estado y planificación del proyecto.
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">

      <span class="badge-vs {{ $badgeEstado }}">
        {{ $estadoLabel }}
      </span>

      <a href="{{ route('admin.proyectos.show', $proyecto->id) }}"
         class="vs-btn vs-btn-light">
        Ver proyecto
      </a>

      <a href="{{ route('admin.proyectos') }}"
         class="vs-btn vs-btn-light">
        ← Volver
      </a>

    </div>

  </div>

  @if ($errors->any())
    <div class="mb-4 rounded-3xl border border-red-200 bg-red-50 px-5 py-4 text-red-800 font-black">
      {{ $errors->first() }}
    </div>
  @endif

  @if(session('ok'))
    <div class="mb-4 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 font-black">
      {{ session('ok') }}
    </div>
  @endif

  <div class="vs-card">

    <div class="vs-card-head">
      <div class="vs-card-title">Información general</div>
      <div class="vs-card-sub">
        Modifica la información principal del proyecto.
      </div>
    </div>

    <form method="POST"
          action="{{ route('admin.proyectos.update', $proyecto->id) }}"
          class="vs-card-body">

      @csrf
      @method('PUT')

      <div class="form-grid">

        <div class="col-4">
          <label class="label-vs">Código</label>

          <input
            name="codigo"
            value="{{ old('codigo', $proyecto->codigo) }}"
            class="input-vs mt-2"
            placeholder="PR-001"
          >
        </div>

        <div class="col-4">
          <label class="label-vs">Estado</label>

          <select name="estado" class="select-vs mt-2">

            <option value="planeado"
              @selected(old('estado',$proyecto->estado)=='planeado')>
              Planeado
            </option>

            <option value="en_ejecucion"
              @selected(old('estado',$proyecto->estado)=='en_ejecucion')>
              En ejecución
            </option>

            <option value="pausado"
              @selected(old('estado',$proyecto->estado)=='pausado')>
              Pausado
            </option>

            <option value="finalizado"
              @selected(old('estado',$proyecto->estado)=='finalizado')>
              Finalizado
            </option>

          </select>
        </div>

        <div class="col-4">
          <label class="label-vs">Responsable</label>

          <select name="responsable_id" class="select-vs mt-2">

            <option value="">— Selecciona —</option>

            @foreach($usuarios as $u)
              <option
                value="{{ $u->id }}"
                @selected((string) old('responsable_id', $proyecto->responsable_id) === (string) $u->id)
              >
                {{ $u->{$nameField} ?? ('Usuario #' . $u->id) }}
              </option>
            @endforeach

          </select>
        </div>

        <div class="col-12">
          <label class="label-vs">Nombre</label>

          <input
            name="nombre"
            value="{{ old('nombre', $proyecto->nombre) }}"
            required
            class="input-vs mt-2"
            placeholder="Nombre del proyecto"
          >
        </div>

        <div class="col-12">
          <label class="label-vs">Descripción</label>

          <textarea
            name="descripcion"
            class="textarea-vs mt-2"
            placeholder="Descripción general..."
          >{{ old('descripcion', $proyecto->descripcion) }}</textarea>
        </div>

        <div class="col-6">
          <label class="label-vs">Ubicación</label>

          <input
            name="ubicacion"
            value="{{ old('ubicacion', $proyecto->ubicacion) }}"
            class="input-vs mt-2"
            placeholder="Ubicación"
          >
        </div>

        <div class="col-6">
          <label class="label-vs">Presupuesto</label>

          <input
            type="number"
            step="0.01"
            name="presupuesto"
            value="{{ old('presupuesto', $proyecto->presupuesto) }}"
            class="input-vs mt-2"
          >
        </div>

        <div class="col-6">
          <label class="label-vs">Fecha inicio</label>

          <input
            type="date"
            name="fecha_inicio"
            value="{{ old('fecha_inicio', optional($proyecto->fecha_inicio)->format('Y-m-d')) }}"
            class="input-vs mt-2"
          >
        </div>

        <div class="col-6">
          <label class="label-vs">Fecha fin</label>

          <input
            type="date"
            name="fecha_fin"
            value="{{ old('fecha_fin', optional($proyecto->fecha_fin)->format('Y-m-d')) }}"
            class="input-vs mt-2"
          >
        </div>

        <div class="col-6">
          <label class="label-vs">Porcentaje avance</label>

          <input
            type="number"
            step="0.01"
            min="0"
            max="100"
            name="porcentaje"
            value="{{ old('porcentaje', $proyecto->porcentaje ?? 0) }}"
            class="input-vs mt-2"
          >
        </div>

        <div class="col-6 flex items-end">
          <label class="flex items-center gap-3 h-[42px] px-4 rounded-2xl border border-slate-200 bg-slate-50 w-full cursor-pointer">

            <input
              type="checkbox"
              name="activo"
              value="1"
              class="rounded border-slate-300"
              @checked(old('activo', $proyecto->activo))
            >

            <span class="text-sm font-black text-slate-700">
              Proyecto activo
            </span>

          </label>
        </div>

      </div>

      <div class="mt-6 flex justify-between items-center flex-wrap gap-3">
          
          {{-- 

        <form method="POST"
              action="{{ route('admin.proyectos.destroy', $proyecto->id) }}"
              onsubmit="return confirm('¿Seguro que deseas eliminar este proyecto?');">

          @csrf
          @method('DELETE')

          <button type="submit" class="vs-btn vs-btn-danger">
            Eliminar proyecto
          </button>

        </form>
--}}
        <div class="flex gap-2">

          <a href="{{ route('admin.proyectos') }}"
             class="vs-btn vs-btn-light">
            Cancelar
          </a>

          <button type="submit" class="vs-btn vs-btn-primary">
            Guardar cambios
          </button>

        </div>

      </div>

    </form>

  </div>

</div>
@endsection
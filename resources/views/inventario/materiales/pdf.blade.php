<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Materiales PDF</title>

<style>

body{
  font-family: DejaVu Sans, sans-serif;
  font-size:10px;
  color:#0f172a;
  margin:20px;
}

.header{
  border-bottom:2px solid #0f172a;
  padding-bottom:10px;
  margin-bottom:12px;
}

.title{
  font-size:22px;
  font-weight:bold;
  margin:0;
  color:#0f172a;
}

.sub{
  color:#64748b;
  margin-top:4px;
  font-size:10px;
}

.logo{
  width:78px;
  max-height:58px;
  object-fit:contain;
}

.info{
  width:100%;
  margin-bottom:12px;
  border-collapse:collapse;
}

.info td{
  padding:6px;
  border:1px solid #e2e8f0;
}

.label{
  font-weight:bold;
  background:#f8fafc;
  width:120px;
}

table{
  width:100%;
  border-collapse:collapse;
}

th{
  background:#0f172a;
  color:white;
  padding:8px;
  text-align:left;
  font-size:9px;
}

td{
  border:1px solid #e2e8f0;
  padding:7px;
  font-size:9px;
}

tbody tr:nth-child(even){
  background:#f8fafc;
}

.right{
  text-align:right;
}

.center{
  text-align:center;
}

.footer{
  margin-top:14px;
  font-size:9px;
  color:#64748b;
  text-align:right;
  border-top:1px solid #cbd5e1;
  padding-top:8px;
}

.badge{
  display:inline-block;
  padding:3px 7px;
  border-radius:999px;
  font-size:8px;
  font-weight:bold;
}

.badge-ok{
  background:#dcfce7;
  color:#166534;
}

.badge-off{
  background:#fee2e2;
  color:#991b1b;
}

.money{
  color:#4338ca;
  font-weight:bold;
}

</style>
</head>

<body>

@php
  $logo = null;

  if (!empty($empresa?->logo_path)) {

      $logoPath = public_path('storage/' . ltrim($empresa->logo_path, '/'));

      if (file_exists($logoPath)) {
          $logo = $logoPath;
      }
  }
@endphp

<div class="header">

  <table style="width:100%; border-collapse:collapse;">
    <tr>

      <td style="border:none; padding:0; vertical-align:top;">

        <h1 class="title">
          Catálogo de materiales
        </h1>

        <div class="sub">
          {{ $empresa->nombre ?? 'Empresa' }}
        </div>

        <div class="sub">
          Generado por:
          <strong>{{ auth()->user()->name ?? 'Usuario' }}</strong>
        </div>

        <div class="sub">
          Fecha reporte:
          {{ now()->format('d/m/Y h:i A') }}
        </div>

      </td>

      <td style="border:none; padding:0; text-align:right; width:90px;">

        @if($logo)
          <img src="{{ $logo }}" class="logo">
        @endif

      </td>

    </tr>
  </table>

</div>

<table class="info">

  <tr>

    <td class="label">
      Búsqueda aplicada
    </td>

    <td>
      {{ $q ?: 'Todas' }}
    </td>

    <td class="label">
      Total materiales
    </td>

    <td>
      {{ number_format(count($materiales)) }}
    </td>

  </tr>

</table>

<table>

  <thead>

    <tr>
      <th>Código</th>
      <th>SKU</th>
      <th>Descripción</th>
      <th>Unidad</th>
      <th class="right">Costo estándar</th>
      <th>Estado</th>
    </tr>

  </thead>

  <tbody>

    @forelse($materiales as $m)

      <tr>

        <td>
          {{ $m->codigo ?: '—' }}
        </td>

        <td>
          {{ $m->sku ?: '—' }}
        </td>

        <td>
          {{ $m->descripcion ?: '—' }}
        </td>

        <td>
          {{ $m->unidad ?: '—' }}
        </td>

        <td class="right money">
          ${{ number_format((float)($m->costo_estandar ?? 0), 2) }}
        </td>

        <td>

          @if($m->activo)
            <span class="badge badge-ok">
              ACTIVO
            </span>
          @else
            <span class="badge badge-off">
              INACTIVO
            </span>
          @endif

        </td>

      </tr>

    @empty

      <tr>
        <td colspan="6" class="center">
          No hay materiales registrados.
        </td>
      </tr>

    @endforelse

  </tbody>

</table>

<div class="footer">
  VerticeSoft ERP / CRM Construcción
</div>

</body>
</html>
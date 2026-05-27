<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Kárdex PDF</title>

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
    width:110px;
  }

  table{
    width:100%;
    border-collapse:collapse;
  }

  th{
    background:#0f172a;
    color:white;
    padding:7px;
    text-align:left;
    font-size:9px;
  }

  td{
    border:1px solid #e2e8f0;
    padding:6px;
    font-size:9px;
  }

  tbody tr:nth-child(even){
    background:#f8fafc;
  }

  .right{text-align:right}
  .center{text-align:center}

  .entrada{color:#047857;font-weight:bold}
  .salida{color:#b91c1c;font-weight:bold}
  .saldo{color:#1d4ed8;font-weight:bold}

  .footer{
    margin-top:14px;
    font-size:9px;
    color:#64748b;
    text-align:right;
    border-top:1px solid #cbd5e1;
    padding-top:8px;
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
        <h1 class="title">Kárdex valorizado</h1>

        <div class="sub">
          {{ $empresa->nombre ?? 'Empresa' }}
        </div>

        <div class="sub">
          Generado por: <strong>{{ auth()->user()->name ?? 'Usuario' }}</strong>
        </div>

        <div class="sub">
          Fecha reporte: {{ now()->format('d/m/Y h:i A') }}
        </div>
      </td>

      <td style="border:none; padding:0; text-align:right; vertical-align:top; width:90px;">
        @if($logo)
          <img src="{{ $logo }}" class="logo">
        @endif
      </td>
    </tr>
  </table>
</div>

<table class="info">
  <tr>
    <td class="label">Material</td>
    <td>{{ $material->sku ?? $material->codigo ?? '' }} — {{ $material->descripcion ?? '' }}</td>

    <td class="label">Almacén</td>
    <td>{{ $almacen->codigo ?? '' }} — {{ $almacen->nombre ?? '' }}</td>
  </tr>

  <tr>
    <td class="label">Desde</td>
    <td>{{ $desde ?: 'Inicio' }}</td>

    <td class="label">Hasta</td>
    <td>{{ $hasta ?: 'Actual' }}</td>
  </tr>
</table>

<table>
  <thead>
    <tr>
      <th>Fecha</th>
      <th>Tipo</th>
      <th>Referencia</th>
      <th class="right">Entrada</th>
      <th class="right">Salida</th>
      <th class="right">Saldo</th>
      <th class="right">Costo U.</th>
      <th class="right">Valor</th>
      <th class="right">Saldo $</th>
    </tr>
  </thead>

  <tbody>
    @forelse($rows as $r)
      <tr>
        <td>{{ $r['fecha'] }}</td>

        <td class="{{ $r['tipo'] === 'entrada' ? 'entrada' : ($r['tipo'] === 'salida' ? 'salida' : '') }}">
          {{ strtoupper($r['tipo']) }}
        </td>

        <td>{{ $r['referencia'] ?: '—' }}</td>

        <td class="right entrada">
          {{ $r['entrada'] ? number_format((float)$r['entrada'], 2) : '—' }}
        </td>

        <td class="right salida">
          {{ $r['salida'] ? number_format((float)$r['salida'], 2) : '—' }}
        </td>

        <td class="right saldo">
          {{ number_format((float)$r['saldo'], 2) }}
        </td>

        <td class="right">
          ${{ number_format((float)$r['costo'], 2) }}
        </td>

        <td class="right">
          ${{ number_format((float)$r['valor'], 2) }}
        </td>

        <td class="right saldo">
          ${{ number_format((float)$r['saldo_valor'], 2) }}
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="9" class="center">
          No hay movimientos para los filtros seleccionados.
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
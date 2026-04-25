<?php

namespace App\Exports;

use App\Models\CuentaPorPagar;
use App\Support\EmpresaScope;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CuentasExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        $user = auth()->user();

        $scopeEmpresaId = (int) EmpresaScope::getId();
        $userEmpresaId  = (int) ($user->empresa_id ?? 0);

        $empresaId = $scopeEmpresaId ?: $userEmpresaId;

        return CuentaPorPagar::with('proyecto')
            ->whereHas('proyecto', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            })
            ->latest('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Proveedor',
            'Proyecto',
            'Descripción',
            'Monto total',
            'Monto pagado',
            'Saldo',
            'Estado',
            'Fecha registro',
            'Fecha vencimiento',
        ];
    }

    public function map($c): array
    {
        return [
            $c->proveedor,
            $c->proyecto->nombre ?? '',
            $c->descripcion ?? '',
            (float) $c->monto_total,
            (float) $c->monto_pagado,
            (float) $c->saldo,
            ucfirst((string) $c->estado),
            $c->fecha ? $c->fecha->format('Y-m-d') : '',
            $c->fecha_vencimiento ? $c->fecha_vencimiento->format('Y-m-d') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnidadesSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['codigo' => 'UND',  'descripcion' => 'Unidad'],
            ['codigo' => 'SACO', 'descripcion' => 'Saco'],
            ['codigo' => 'KG',   'descripcion' => 'Kilogramo'],
            ['codigo' => 'LBS',  'descripcion' => 'Libra'],
            ['codigo' => 'LTS',  'descripcion' => 'Litro'],
            ['codigo' => 'M',    'descripcion' => 'Metro'],
            ['codigo' => 'M2',   'descripcion' => 'Metro cuadrado'],
            ['codigo' => 'M3',   'descripcion' => 'Metro cúbico'],
            ['codigo' => 'CJ',   'descripcion' => 'Caja'],
            ['codigo' => 'GLN',  'descripcion' => 'Galón'],
        ];

        foreach ($items as $it) {
            DB::table('unidades')->updateOrInsert(
                ['codigo' => $it['codigo']],
                ['descripcion' => $it['descripcion']]
            );
        }
    }
}

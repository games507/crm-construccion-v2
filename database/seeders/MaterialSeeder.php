<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Unidad;
use App\Models\ClaseConstruccion;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $und = Unidad::where('codigo','UND')->firstOrFail();
$kg  = Unidad::where('codigo','KG')->firstOrFail();

$concreto = ClaseConstruccion::where('nombre','Concreto')->first();
$acero    = ClaseConstruccion::where('nombre','Acero')->first();


        Material::updateOrCreate(
            ['sku' => 'MAT-0001'],
            [
                'descripcion' => 'Cemento gris 42.5kg',
                'unidad_id' => $und?->id,
                'clase_construccion_id' => $concreto?->id,
                'costo_estandar' => 8.50,
                'activo' => 1,
            ]
        );

        Material::updateOrCreate(
            ['sku' => 'MAT-0002'],
            [
                'descripcion' => 'Varilla acero 3/8"',
                'unidad_id' => $kg?->id,
                'clase_construccion_id' => $acero?->id,
                'costo_estandar' => 1.25,
                'activo' => 1,
            ]
        );
    }
}

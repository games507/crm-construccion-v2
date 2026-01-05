<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Almacen;

class AlmacenSeeder extends Seeder
{
    public function run(): void
    {
        Almacen::insert([
            ['codigo'=>'ALM-PRIN','nombre'=>'Almacén Principal','ubicacion'=>'Bodega Central'],
            ['codigo'=>'ALM-OBRA','nombre'=>'Almacén Obra','ubicacion'=>'Proyecto Principal'],
        ]);
    }
}

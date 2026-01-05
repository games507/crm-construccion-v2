<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClaseConstruccion;

class ClaseConstruccionSeeder extends Seeder
{
    public function run(): void
    {
        ClaseConstruccion::insert([
            ['nombre'=>'Concreto'],
            ['nombre'=>'Acero'],
            ['nombre'=>'Eléctrico'],
            ['nombre'=>'Plomería'],
            ['nombre'=>'Acabados'],
        ]);
    }
}

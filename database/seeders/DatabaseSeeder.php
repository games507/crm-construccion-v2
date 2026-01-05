<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UnidadSeeder::class,
            ClaseConstruccionSeeder::class,
            AlmacenSeeder::class,
            MaterialSeeder::class,
            UnidadesSeeder::class,
            EmpresaSeeder::class,      // si lo usas
            SuperAdminSeeder::class,  //  nuevo
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;
use App\Models\User;

class EmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::firstOrCreate(
            ['nombre' => 'Constructora Demo, S.A.'],
            [
                'ruc' => '1556789-1-123456',
                'telefono' => '6000-0000',
                'email' => 'info@demo.com',
                'direccion' => 'Panamá, San Miguelito',
                'activa' => true,
            ]
        );

        $u = User::orderBy('id')->first();
        if ($u && !$u->empresa_id) {
            $u->empresa_id = $empresa->id;
            $u->save();
        }
    }
}

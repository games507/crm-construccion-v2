<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $u = User::find(1);
        if ($u) {
            $u->assignRole('Admin');
        }
    }
}

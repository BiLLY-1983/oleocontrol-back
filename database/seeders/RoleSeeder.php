<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrador'],
            ['name' => 'Empleado'],
            ['name' => 'Socio'],
            ['name' => 'Invitado']
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}

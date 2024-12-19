<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Contabilidad'],
            ['name' => 'Laboratorio'],
            ['name' => 'Control de entradas'],
            ['name' => 'RRHH'],
            ['name' => 'Administración'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}

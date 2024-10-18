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
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}

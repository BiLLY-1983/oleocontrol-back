<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los departamentos existentes
        $departments = Department::all();

        // Comprobar si hay departamentos disponibles
        if ($departments->isEmpty()) {
            $this->command->warn('No hay departamentos disponibles. No se pueden asignar empleados.');
            return;
        }

        // Obtener todos los usuarios que tienen el rol 'Trabajador'
        $employees = User::whereHas('roles', fn($query) => $query->where('name', 'Empleado'))->get();

        foreach ($employees as $employee) {
            Employee::create([
                'user_id' => $employee->id,
                'department_id' =>  $departments->random()->id, 
            ]);
        }
    }
}

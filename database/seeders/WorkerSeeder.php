<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkerSeeder extends Seeder
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
            $this->command->warn('No hay departamentos disponibles. No se pueden asignar workers.');
            return;
        }



        // Obtener todos los usuarios que tienen el rol 'worker'
        $workers = User::whereHas('roles', fn($query) => $query->where('name', 'Trabajador'))->get();

        foreach ($workers as $worker) {
            Worker::create([
                'user_id' => $worker->id,
                'department_id' =>  $departments->random()->id, 
            ]);
        }
    }
}

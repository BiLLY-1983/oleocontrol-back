<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* Crear un usuario Admin */
        $admin = User::create([
            'username' => 'BiLLY',
            'first_name' => 'Pedro',
            'last_name' => 'Berzosa Ogallar',
            'dni' => '77342536E',
            'email' => 'oleocontrol.info@gmail.com',
            'password' => Hash::make('PasswordAdmin123'),
            'phone' => '687853157',
            'status' => true,
        ]);

        // El Admin tendrá todos los roles
        $adminRoles = Role::whereIn('name', ['Administrador', 'Socio', 'Empleado'])->get();
        $admin->roles()->attach($adminRoles);

        /* Crear un usuario Admin */
        $admin = User::create([
            'username' => 'AdminPruebas',
            'first_name' => 'Administrador',
            'last_name' => 'Pruebas',
            'dni' => '00000000A',
            'email' => 'admin.pruebas@gmail.com',
            'password' => Hash::make('Password123'),
            'phone' => '000000000',
            'status' => true,
        ]);

        // El Admin tendrá todos los roles
        $adminRoles = Role::whereIn('name', ['Administrador', 'Socio', 'Empleado'])->get();
        $admin->roles()->attach($adminRoles);


        /* ------------------------- */

        /* Crear un usuario Invitado */
        $guess = User::create([
            'username' => 'Guess',
            'first_name' => 'Invitado',
            'last_name' => 'Pruebas',
            'dni' => 'Invitado',
            'email' => 'invitado@gmail.com',
            'password' => Hash::make('Password123'),
            'phone' => '000000000',
            'status' => true,
        ]);

        $guessRole = Role::where('name', 'Invitado')->first();
        $guess->roles()->attach($guessRole);

        /* ------------------------- */

        /* ------------------------- */

        /* Crear un usuario Socio */
        $member = User::create([
            'username' => 'SocioPruebas',
            'first_name' => 'Socio',
            'last_name' => 'Pruebas',
            'dni' => 'member',
            'email' => 'socio.pruebas.oleocontrol@gmail.com',
            'password' => Hash::make('Password123'),
            'phone' => '000000000',
            'status' => true,
        ]);

        $memberRole = Role::where('name', 'Socio')->first();
        $member->roles()->attach($memberRole);

        /* ------------------------- */

        /* Crear usuarios 'Empleado' */

        // Obtener los departamentos por nombre
        $contabilidad = Department::where('name', 'Contabilidad')->first();
        $laboratorio = Department::where('name', 'Laboratorio')->first();
        $controlEntradas = Department::where('name', 'Control de entradas')->first();
        $rrhh = Department::where('name', 'RRHH')->first();
        $administracion = Department::where('name', 'Administración')->first();

        // Crear empleados para cada departamento
        $employeesByDepartment = [
            ['username' => 'Contabilidad_Emp', 'first_name' => 'Empleado', 'last_name' => 'Contabilidad', 'email' => 'contabilidad@gmail.com', 'department_id' => $contabilidad->id],
            ['username' => 'Laboratorio_Emp', 'first_name' => 'Empleado', 'last_name' => 'Laboratorio', 'email' => 'laboratorio@gmail.com', 'department_id' => $laboratorio->id],
            ['username' => 'ControlEntradas_Emp', 'first_name' => 'Empleado', 'last_name' => 'Control Entradas', 'email' => 'entradas@gmail.com', 'department_id' => $controlEntradas->id],
            ['username' => 'RRHH_Emp', 'first_name' => 'Empleado', 'last_name' => 'RRHH', 'email' => 'rrhh@gmail.com', 'department_id' => $rrhh->id],
            ['username' => 'Administracion_Emp', 'first_name' => 'Empleado', 'last_name' => 'Administración', 'email' => 'control.entradas@gmail.com', 'department_id' => $administracion->id],
        ];

        foreach ($employeesByDepartment as $employee) {
            $user = User::create([
                'username' => $employee['username'],
                'first_name' => $employee['first_name'],
                'last_name' => $employee['last_name'],
                'email' => $employee['email'],
                'password' => Hash::make('Password123'),
                'phone' => '654321987',
                'status' => true,
                'department_id' => $employee['department_id'],
            ]);

            $workerRole = Role::where('name', 'Empleado')->first();
            $user->roles()->attach($workerRole);
        }

        /* ------------------------- */

        // Crear otros 100 usuarios
        $usersCreated = User::factory()->count(100)->create();

        // Asignación de roles aleatoriamente
        $memberRole = Role::where('name', 'Socio')->first();
        $workerRole = Role::where('name', 'Empleado')->first();

        foreach ($usersCreated as $user) {

            if (rand(0, 1) == 0) {
                $user->roles()->attach($memberRole);
            } else {
                $user->roles()->attach($workerRole);
            }
        }
    }
}

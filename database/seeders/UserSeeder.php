<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
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

        Employee::create([
            'user_id' => $admin->id,
            'department_id' => 1
        ]);

        /* Crear un usuario Admin */
        $admin = User::create([
            'username' => 'AdminPruebas',
            'first_name' => 'Administrador',
            'last_name' => 'Pruebas',
            'dni' => '00000000P',
            'email' => 'admin.pruebas@gmail.com',
            'password' => Hash::make('Password123'),
            'phone' => '000000000',
            'status' => true,
        ]);

        // El Admin tendrá todos los roles
        $admin->roles()->attach($adminRoles);

        Employee::create([
            'user_id' => $admin->id,
            'department_id' => 1
        ]);


        /* ------------------------- */

        /* Crear un usuario Invitado */
        $guess = User::create([
            'username' => 'Guess',
            'first_name' => 'Invitado',
            'last_name' => 'Pruebas',
            'dni' => '00000000Z',
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
            'dni' => '00000000S',
            'email' => 'socio.oleocontrol@hotmail.com',
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
            ['username' => 'Contabilidad_Emp', 'first_name' => 'Empleado', 'last_name' => 'Contabilidad', 'dni' => '00000000C', 'email' => 'contabilidad@gmail.com', 'department_id' => $contabilidad->id],
            ['username' => 'Laboratorio_Emp', 'first_name' => 'Empleado', 'last_name' => 'Laboratorio', 'dni' => '00000000L', 'email' => 'laboratorio@gmail.com', 'department_id' => $laboratorio->id],
            ['username' => 'ControlEntradas_Emp', 'first_name' => 'Empleado', 'last_name' => 'Control Entradas', 'dni' => '00000000E', 'email' => 'entradas@gmail.com', 'department_id' => $controlEntradas->id],
            ['username' => 'RRHH_Emp', 'first_name' => 'Empleado', 'last_name' => 'RRHH', 'dni' => '00000000R', 'email' => 'rrhh@gmail.com', 'department_id' => $rrhh->id],
            ['username' => 'Administracion_Emp', 'first_name' => 'Empleado', 'last_name' => 'Administración', 'dni' => '00000000A', 'email' => 'control.entradas@gmail.com', 'department_id' => $administracion->id],
        ];

        foreach ($employeesByDepartment as $employee) {
            $user = User::create([
                'username' => $employee['username'],
                'first_name' => $employee['first_name'],
                'last_name' => $employee['last_name'],
                'dni' => $employee['dni'],
                'email' => $employee['email'],
                'password' => Hash::make('Password123'),
                'phone' => '687853157',
                'status' => true,
            ]);

            $workerRole = Role::where('name', 'Empleado')->first();
            $user->roles()->attach($workerRole);

            // Crear la entrada correspondiente en la tabla employees
            Employee::create([
                'user_id' => $user->id,  
                'department_id' => $employee['department_id']
            ]);
        }

        /* ------------------------- */

        // Crear otros 100 usuarios
        $usersCreated = User::factory()->count(100)->create();

        // Asignación de roles aleatoriamente
        $memberRole = Role::where('name', 'Socio')->first();

        foreach ($usersCreated as $user) {
            $user->roles()->attach($memberRole); 
        }
    }
}

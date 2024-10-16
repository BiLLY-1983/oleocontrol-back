<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use App\Models\Worker;
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
            'username' => 'Admin',
            'first_name' => 'Pedro',
            'last_name' => 'Berzosa Ogallar',
            'dni' => '77342536E',
            'email' => 'pberzosa83@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '687853157',
            'status' => true,
            'profile_picture' => 'profile_pictures/admin.jpg'
        ]);
    
        $adminRole = Role::where('name', 'Administrador')->first();
        $admin->roles()->attach($adminRole);
        

        /* ------------------------- */

        /* Crear un usuario Invitado */
        $guess = User::create([
            'username' => 'Guess',
            'first_name' => 'Invitado',
            'last_name' => '.',
            'dni' => 'Invitado',
            'email' => 'invitado@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '111111111',
            'status' => true,
            'profile_picture' => 'profile_pictures/guess.jpg'
        ]);
   
        $guessRole = Role::where('name', 'Invitado')->first();
        $guess->roles()->attach($guessRole);

        /* ------------------------- */

        /* ------------------------- */

        /* Crear un usuario Socio */
        $member = User::create([
            'username' => 'Member',
            'first_name' => 'Prueba',
            'last_name' => '.',
            'dni' => 'member',
            'email' => 'socio@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '111111111',
            'status' => true,
            'profile_picture' => 'profile_pictures/guess.jpg'
        ]);
   
        $memberRole = Role::where('name', 'Socio')->first();
        $member->roles()->attach($memberRole);

        /* ------------------------- */

        /* Crear un usuario Trabajador */
        $worker = User::create([
            'username' => 'Worker',
            'first_name' => 'Worker',
            'last_name' => '.',
            'dni' => 'worker',
            'email' => 'worker@gmail.com',
            'password' => bcrypt('password'),
            'phone' => '111111111',
            'status' => true,
            'profile_picture' => 'profile_pictures/guess.jpg'
        ]);
   
        $workerRole = Role::where('name', 'Trabajador')->first();
        $worker->roles()->attach($workerRole);

        /* ------------------------- */

        // Crear otros 100 usuarios
        $usersCreated = User::factory()->count(100)->create();

        // Asignación de roles aleatoriamente
        $memberRole = Role::where('name', 'Socio')->first();
        $workerRole = Role::where('name', 'Trabajador')->first();

        foreach ($usersCreated as $user) {

            if (rand(0, 1) == 0) {
                $user->roles()->attach($memberRole);
            } else {
                $user->roles()->attach($workerRole);  
            }
        }
    }
}

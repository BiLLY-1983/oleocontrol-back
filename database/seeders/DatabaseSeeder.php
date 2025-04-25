<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            OilSeeder::class, 
            UserSeeder::class,
            //EmployeeSeeder::class,
            MemberSeeder::class,
            EntrySeeder::class,
            //SettlementSeeder::class,
            //NotificationSeeder::class,
        ]);
    }
}

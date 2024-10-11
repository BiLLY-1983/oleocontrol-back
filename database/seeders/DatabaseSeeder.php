<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            OilSeeder::class,
            RoleSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            WorkerSeeder::class,
            MemberSeeder::class,
            EntrySeeder::class,
            AnalysisSeeder::class,
            SettlementSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}

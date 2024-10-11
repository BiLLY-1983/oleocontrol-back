<?php

namespace Database\Seeders;

use App\Models\Settlement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettlementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Settlement::factory(50)->create();
    }
}

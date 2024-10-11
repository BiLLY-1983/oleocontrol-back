<?php

namespace Database\Seeders;

use App\Models\Analysis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnalysisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Analysis::factory(10)->create();
    }
}

<?php

namespace Database\Seeders;

use App\Models\Analysis;
use App\Models\Entry;
use Illuminate\Database\Seeder;

class EntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generar 100 entradas de aceituna
        Entry::factory()->count(100)->create()->each(function ($entry) {
            // Para cada entrada, generar un análisis y asociarlo a la entrada
            Analysis::factory()->create([
                'entry_id' => $entry->id,
            ]);
        });
    }
}

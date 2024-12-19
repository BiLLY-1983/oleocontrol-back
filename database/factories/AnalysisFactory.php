<?php

namespace Database\Factories;

use App\Models\Entry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Analysis>
 */
class AnalysisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Obtener todos los IDs de las entradas
        $entryIds = Entry::pluck('id')->toArray();

        return [
            'analysis_date' => null,
            'acidity' => null,
            'humidity' => null,
            'yield' => null,
            'entry_id' => null,
            'employee_id' => null,
            'oil_id' => null, 
        ];
    }
}

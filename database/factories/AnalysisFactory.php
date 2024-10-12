<?php

namespace Database\Factories;

use App\Models\Entry;
use App\Models\Oil;
use App\Models\Worker;
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

        // Elegir un ID aleatorio y único
        $entryId = $this->faker->unique()->randomElement($entryIds);

        return [
            'analysis_date' => null,
            'acidity' => null,
            'humidity' => null,
            'yield' => null,
            'entry_id' => $entryId,
            'worker_id' => null,
            'oil_id' => null, 
            'oil_quantity' => null,
        ];
    }
}

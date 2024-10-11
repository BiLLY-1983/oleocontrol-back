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
        return [
            'analysis_date' => null,
            'acidity' => null,
            'humidity' => null,
            'yield' => null,
            'entry_id' => Entry::inRandomOrder()->first()->id, 
            'worker_id' => null,
            'oil_id' => null, 
            'oil_quantity' => null,
        ];
    }
}

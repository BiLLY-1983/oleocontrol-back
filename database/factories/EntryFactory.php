<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entry>
 */
class EntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'entry_date' => $this->faker->date(),
            'quantity' => $this->faker->numberBetween(1, 10000),
            'analysis_status' => $this->faker->randomElement(['Pendiente', 'Completo']),
            'member_id' => Member::inRandomOrder()->first()->id, 
        ];
    }
}

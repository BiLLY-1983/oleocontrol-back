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
        // Obtener un miembro aleatorio, asegurando que existen miembros
        $member = Member::inRandomOrder()->first();

        return [
            'entry_date' => $this->faker->date(),
            'quantity' => $this->faker->numberBetween(1, 10000),
            'analysis_status' => 'Pendiente',
            'member_id' => $member->id, 
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\employee;
use App\Models\Oil;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Settlement>
 */
class SettlementFactory extends Factory
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
        $oil = Oil::inRandomOrder()->first();

        return [
            'oil_id' => $oil->id, 
            'settlement_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 100, 1000), 
            'price' => $this->faker->randomFloat(2, 10, 500),    
            'settlement_status' => 'Pendiente',
            'member_id' => $member->id, 
            'employee_id' => null, 

        ];
    }
}

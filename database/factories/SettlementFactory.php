<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Worker;
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
        return [
            'amount' => $this->faker->randomFloat(2, 100, 10000), 
            'price' => $this->faker->randomFloat(2, 10, 500),    
            'settlement_status' => $this->faker->randomElement(['Pending', 'Paid', 'Cancelled']),
            'member_id' => Member::inRandomOrder()->first()->id, 
            'worker_id' => null, 

        ];
    }
}

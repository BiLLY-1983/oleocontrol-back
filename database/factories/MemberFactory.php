<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $users = User::all()->pluck('id')->toArray();

        return [
            'member_number' => $this->faker->unique()->numberBetween(1000, 9999),
            'user_id' => $this->faker->unique()->randomElement($users), 
        ];
    }
}

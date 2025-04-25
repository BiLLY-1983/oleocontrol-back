<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $dni = $this->generarDNI();

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'dni' => $dni,
            'username' => User::generateUsername($firstName, $lastName, $dni),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('Password123'),
            'phone' => $this->faker->phoneNumber(),
            'status' => true,
        ];
    }


    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Función para generar un DNI español aleatorio.
     * @return string
     */
    private function generarDNI(): string
    {
        $numero = rand(10000000, 99999999);
        $letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $letra = $letras[$numero % 23];
        return $numero . $letra;
    }
}

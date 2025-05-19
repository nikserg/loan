<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'age' => fake()->numberBetween(18, 60),
            'city' => fake()->city(),
            'region' => fake()->randomElement(['PR', 'BR', 'OS']),
            'income' => fake()->randomFloat(2, 1000, 5000),
            'score' => fake()->numberBetween(300, 850),
            'pin' => fake()->unique()->numerify('###-##-####'),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}

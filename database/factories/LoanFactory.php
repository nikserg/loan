<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+30 days');
        $endDate = fake()->dateTimeBetween($startDate, '+1 year');
        
        return [
            'client_id' => Client::factory(),
            'name' => fake()->randomElement(['Personal Loan', 'Home Loan', 'Auto Loan', 'Business Loan']),
            'amount' => fake()->randomFloat(2, 1000, 50000),
            'rate' => fake()->randomFloat(2, 5, 25),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 10, 500),
            'description' => fake()->sentence(3),
            'transaction_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
        ];
    }
}

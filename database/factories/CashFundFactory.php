<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CashFund>
 */
class CashFundFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->randomFloat(2, 100, 10000),
            'note' => fake()->optional()->sentence(),
            'fund_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Create as initial fund setup.
     */
    public function initial(): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => 'Initial petty cash fund',
        ]);
    }

    /**
     * Create as replenishment.
     */
    public function replenishment(): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => 'Fund replenishment',
        ]);
    }
}

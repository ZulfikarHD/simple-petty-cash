<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'icon' => fake()->randomElement(['shopping-bag', 'utensils', 'car', 'box', 'more-horizontal']),
            'color' => fake()->hexColor(),
            'is_default' => false,
            'user_id' => User::factory(),
        ];
    }

    /**
     * Mark category as default (system-wide).
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
            'user_id' => null,
        ]);
    }
}

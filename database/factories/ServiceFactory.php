<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Hair Cut',
                'Beard Trim',
                'Hair Wash',
                'Shave',
                'Hair Styling',
                'Eyebrow Trim',
                'Hair Coloring',
                'Hair Treatment',
                'Scalp Massage',
                'Kids Hair Cut'
            ]),
            'price' => fake()->randomFloat(2, 10, 150),
            'description' => fake()->optional()->paragraph(),
            'is_active' => fake()->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Indicate that the service should be inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a premium service with higher price.
     */
    public function premium(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => fake()->randomFloat(2, 80, 200),
        ]);
    }
}

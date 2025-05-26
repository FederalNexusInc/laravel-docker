<?php

namespace Database\Factories;

use App\Models\Helix;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Helix>
 */
class HelixFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'anchor_id' => 1, // Or generate dynamically
            'description' => 'Size ' . $this->faker->numberBetween(6, 12) . ' Thickness ' . $this->faker->randomElement(['1/4', '3/8', '1/2']),
            'size' => $this->faker->randomFloat(1, 6, 12),
            'thickness' => $this->faker->randomElement([0.25, 0.375, 0.5]),
            'rating' => $this->faker->numberBetween(30000, 70000),
            'helix_count' => $this->faker->numberBetween(1, 5),
        ];
    }
}
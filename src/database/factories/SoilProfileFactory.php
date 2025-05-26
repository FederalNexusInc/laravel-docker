<?php

namespace Database\Factories;

use App\Models\SoilProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SoilProfile>
 */
class SoilProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => 1, // Or generate dynamically
            'maximum_depth' => $this->faker->randomFloat(1, 10, 100),
            'water_table_depth' => $this->faker->randomFloat(1, 5, 50),
            'soil_type' => $this->faker->randomElement(['non-cohesive', 'cohesive', 'mixed']),
        ];
    }
}
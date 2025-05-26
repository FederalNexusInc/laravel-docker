<?php

namespace Database\Factories;

use App\Models\SoilLayer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SoilLayer>
 */
class SoilLayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'soil_profile_id' => 1, // Or generate dynamically
            'start_depth' => $this->faker->randomFloat(1, 0, 50),
            'blow_count' => $this->faker->numberBetween(1, 30),
            'soil_layer_type_id' => 1, // Or generate dynamically
            'cohesion' => $this->faker->randomFloat(2, 0, 100),
            'coefficient_of_adhesion' => $this->faker->randomFloat(2, 0, 1),
            'angle_of_internal_friction' => $this->faker->randomFloat(1, 0, 45),
            'coefficient_of_external_friction' => $this->faker->randomFloat(2, 0, 1),
            'moist_unit_weight' => $this->faker->randomFloat(1, 80, 120),
            'saturated_unit_weight' => $this->faker->randomFloat(1, 90, 130),
            'nc' => $this->faker->randomFloat(2, 0, 200),
            'nq' => $this->faker->randomFloat(2, 0, 200),
        ];
    }
}
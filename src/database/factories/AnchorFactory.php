<?php

namespace Database\Factories;

use App\Models\Anchor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Anchor>
 */
class AnchorFactory extends Factory
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
            'lead_shaft_od' => '2 7/8',
            'lead_shaft_length' => $this->faker->randomFloat(1, 5, 20),
            'extension_shaft_od' => '2 7/8',
            'wall_thickness' => $this->faker->randomFloat(3, 0.1, 0.3),
            'yield_strength' => $this->faker->randomFloat(1, 50, 80),
            'tensile_strength' => $this->faker->randomFloat(1, 70, 100),
            'empirical_torque_factor' => $this->faker->randomFloat(1, 5, 15),
            'required_allowable_capacity' => $this->faker->randomFloat(1, 15, 30),
            'anchor_type' => $this->faker->numberBetween(1, 3),
            'required_safety_factor' => $this->faker->randomFloat(1, 1.5, 3),
            'anchor_declination_degree' => $this->faker->numberBetween(15, 45),
            'pile_head_position' => $this->faker->randomFloat(1, 3, 8),
            'x1' => $this->faker->randomFloat(1, 5, 15),
            'y1' => $this->faker->randomFloat(1, 10, 20),
            'x2' => $this->faker->randomFloat(1, 15, 25),
            'y2' => $this->faker->randomFloat(1, 15, 25),
            'x3' => $this->faker->randomFloat(1, 25, 35),
            'y3' => $this->faker->randomFloat(1, 25, 35),
            'x4' => $this->faker->randomFloat(1, 35, 45),
            'y4' => $this->faker->randomFloat(1, 35, 45),
            'x5' => $this->faker->randomFloat(1, 45, 55),
            'y5' => $this->faker->randomFloat(1, 45, 55),
            'omit_shaft_resistance' => $this->faker->numberBetween(0, 1),
            'omit_helix_mechanical_strength_check' => $this->faker->numberBetween(0, 1),
            'omit_shaft_mechanical_strength_check' => $this->faker->numberBetween(0, 1),
            'field_notes' => $this->faker->optional()->sentence, // Optional field notes
        ];
    }
}
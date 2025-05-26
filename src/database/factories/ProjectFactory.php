<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_name' => 'Test ' . $this->faker->numberBetween(1, 100), // Example with dynamic project name
            'project_number' => $this->faker->numerify('###'),
            'run_id' => $this->faker->numerify('###'),
            'soil_reporter' => $this->faker->name,
            'soil_report_number' => $this->faker->numberBetween(1, 100),
            'soil_report_date' => $this->faker->date(),
            'pile_type' => $this->faker->randomElement(['guy_anchor', 'helical_pile', 'driven_pile']),
            'boring_number' => $this->faker->numberBetween(1, 20),
            'boring_log_date' => $this->faker->date(),
            'termination_depth' => $this->faker->numberBetween(10, 100),
            'project_address' => $this->faker->streetAddress,
            'project_city' => $this->faker->city,
            'project_state' => $this->faker->stateAbbr,
            'project_zip_code' => $this->faker->postcode,
            'remarks' => $this->faker->sentence,
            'created_by' => 1, // Or dynamically generate if needed
        ];
    }
}
<?php

namespace Database\Factories;

use App\Models\SoilLayerType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SoilLayerType>
 */
class SoilLayerTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Sand', 'Clay', 'Peat']),
        ];
    }
}
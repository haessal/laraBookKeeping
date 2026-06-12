<?php

namespace Database\Factories\DataProvider\Eloquent;

use App\Models\DataProvider\Eloquent\Slip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slip>
 */
class SlipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slip_outline' => fake()->sentence(),
            'date' => fake()->date(),
        ];
    }
}

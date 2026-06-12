<?php

namespace Database\Factories\DataProvider\Eloquent;

use App\Models\DataProvider\Eloquent\SlipEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SlipEntry>
 */
class SlipEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'amount' => fake()->numberBetween(1, 10000),
            'client' => fake()->name(),
            'outline' => fake()->sentence(),
        ];
    }
}

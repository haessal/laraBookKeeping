<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SlipEntry>
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
            'slip_entry_id' => fake()->uuid(),
            'slip_id' => fake()->uuid(),
            'debit' => fake()->uuid(),
            'credit' => fake()->uuid(),
            'amount' => fake()->numberBetween(1),
            'client' => fake()->word(),
            'outline' => fake()->sentence(),
        ];
    }
}

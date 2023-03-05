<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SlipGroupEntry>
 */
class SlipGroupEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slip_group_entry_id' => fake()->uuid(),
            'slip_group_id' => fake()->uuid(),
            'related_slip' => fake()->uuid(),
        ];
    }
}

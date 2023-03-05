<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SlipGroup>
 */
class SlipGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slip_group_id' => fake()->uuid(),
            'book_id' => fake()->uuid(),
            'slip_group_outline' => fake()->sentence(),
            'slip_group_memo' => fake()->paragraph(),
        ];
    }
}

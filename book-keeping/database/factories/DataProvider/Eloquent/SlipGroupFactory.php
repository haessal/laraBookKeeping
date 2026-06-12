<?php

namespace Database\Factories\DataProvider\Eloquent;

use App\Models\DataProvider\Eloquent\SlipGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SlipGroup>
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

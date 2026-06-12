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
            'slip_id' => fake()->uuid(),
            'book_id' => fake()->uuid(),
            'slip_outline' => fake()->sentence(),
            'slip_memo' => fake()->paragraph(),
            'date' => fake()->date(),
            'is_draft' => fake()->boolean(),
        ];
    }
}

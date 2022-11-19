<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slip>
 */
class SlipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
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

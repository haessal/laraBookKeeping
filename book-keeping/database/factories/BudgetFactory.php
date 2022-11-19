<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'budget_id' => fake()->uuid(),
            'book_id' => fake()->uuid(),
            'account_code' => fake()->uuid(),
            'date' => fake()->date(),
            'amount' => fake()->randomNumber(),
        ];
    }
}

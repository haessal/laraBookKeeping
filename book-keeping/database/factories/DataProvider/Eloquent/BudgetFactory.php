<?php

namespace Database\Factories\DataProvider\Eloquent;

use App\Models\DataProvider\Eloquent\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'budget_id' => fake()->uuid(),
            'book_id' => fake()->uuid(),
            'account_code' => fake()->uuid(),
            'date' => fake()->date(),
            'amount' => fake()->numberBetween(1),
        ];
    }
}

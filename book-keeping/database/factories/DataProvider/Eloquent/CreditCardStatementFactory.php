<?php

namespace Database\Factories\DataProvider\Eloquent;

use App\Models\DataProvider\Eloquent\CreditCardStatement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CreditCardStatement>
 */
class CreditCardStatementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'credit_card_statement_outline' => fake()->sentence(),
            'date' => fake()->date(),
        ];
    }
}

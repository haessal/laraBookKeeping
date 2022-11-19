<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountGroup>
 */
class AccountGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_group_id' => fake()->uuid(),
            'book_id' => fake()->uuid(),
            'account_type' => fake()->randomElement(['asset', 'liability', 'expense', 'revenue']),
            'account_group_title' => fake()->word(),
            'is_current' => fake()->boolean(),
        ];
    }
}

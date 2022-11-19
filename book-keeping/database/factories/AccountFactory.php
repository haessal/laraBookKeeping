<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => fake()->uuid(),
            'account_group_id' => fake()->uuid(),
            'account_title' => fake()->word(),
            'description' => fake()->sentence(),
            'selectable' => fake()->boolean(),
        ];
    }
}

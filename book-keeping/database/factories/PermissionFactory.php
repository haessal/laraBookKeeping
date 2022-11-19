<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'permission_id' => fake()->uuid(),
            'permitted_user' => fake()->randomNumber(),
            'readable_book' => fake()->uuid(),
            'modifiable' => fake()->boolean(),
            'is_owner' => fake()->boolean(),
            'is_default' => fake()->boolean(),
        ];
    }
}

<?php

namespace Database\Factories\DataProvider\Eloquent;

use App\Models\DataProvider\Eloquent\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
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

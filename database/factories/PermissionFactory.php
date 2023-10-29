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
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $view = fake()->randomElement(['v', '-']);
        $add = fake()->randomElement(['a', '-']);
        $edit = fake()->randomElement(['e', '-']);
        $delete = fake()->randomElement(['d', '-']);
        if ($view == '-') {
            $add = '-';
            $edit = '-';
            $delete = '-';
        }
        return [
            'user_permission' => $view . $add . $edit . $delete
        ];
    }
}

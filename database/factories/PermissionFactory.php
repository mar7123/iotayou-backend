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
        $rnd = fake()->boolean(70);
        $view = '-';
        if($rnd){
            $view = 'v';
        }
        $add = fake()->randomElement(['a', '-']);
        $edit = fake()->randomElement(['e', '-']);
        $delete = fake()->randomElement(['d', '-']);
        if ($view == '-') {
            $add = '-';
            $edit = '-';
            $delete = '-';
        }
        return [
            'role_permission' => $view . $add . $edit . $delete
        ];
    }
}

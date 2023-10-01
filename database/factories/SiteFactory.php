<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Site>
 */
class SiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lat = fake()->latitude(-8, -5);
        $long = fake()->longitude(105, 114);
        return [
            "customer_id" => fake()->randomElement(User::where('user_type', 3)->pluck('user_id')),
            "code" => fake()->firstName(),
            "name" => fake()->name(),
            "address" => fake()->address(),
            "sourceloc" => fake()->numberBetween(1, 100),
            "location" => "{$lat}, {$long}",
            "pic" => fake()->firstName(),
            "status" => 6,
        ];
    }
}

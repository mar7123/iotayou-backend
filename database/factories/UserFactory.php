<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserGroups;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => fake()->userName(),
            'email' => fake()->safeEmail(),
            'name' => fake()->name(),
            'notes' => fake()->text(50),
            'phone_num' => fake()->phoneNumber(),
            'picture' => fake()->imageUrl(640, 480),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

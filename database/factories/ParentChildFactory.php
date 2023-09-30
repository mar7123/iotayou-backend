<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ParentChild>
 */
class ParentChildFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $parent_id = fake()->randomElement(User::where('user_type', '<', 3)->pluck('user_id'));
        // $parent_type = User::select('user_type')->where('user_id', $parent_id)->first();
        // $child_id = fake()->randomElement(User::where('user_type', $parent_type->user_type + 1)->pluck('user_id'));
        return [
            // 'parent_id' => $parent_id,
            // 'child_id' => $child_id
        ];
    }
}

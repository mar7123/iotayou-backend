<?php

namespace Database\Factories;

use App\Models\ParentChild;
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
        $salt = Str::random(10);
        $user_type = fake()->numberBetween(1, User::max('user_type'));
        $user_type = $user_type + 1;
        if ($user_type > 3) {
            $user_type = 3;
        }
        $passw = UserGroups::select('group_code')->where('user_group_id', $user_type)->first();
        return [
            'username' => fake()->userName(),
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone_num' => fake()->phoneNumber(),
            'pic' => fake()->firstName(),
            'address' => fake()->address(),
            'salt' => $salt,
            'password' => bcrypt($passw->group_code . '123' . $salt),
            'user_type' => $user_type
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $parent_id = User::select('user_id')->where('user_type', $user->user_type - 1)->get();
            $randnum = rand(0, $parent_id->count() - 1);
            ParentChild::create([
                'parent_id' => $parent_id[$randnum]->user_id,
                'child_id' => $user->user_id
            ]);
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

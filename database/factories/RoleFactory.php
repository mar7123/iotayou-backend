<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\UserGroups;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $role_type = fake()->numberBetween(1, Role::max('role_type'));
        $role_type = $role_type + 1;
        if ($role_type > 3) {
            $role_type = 3;
        }
        $passw = UserGroups::select('group_code')->where('user_group_id', $role_type)->first();
        return [
            'code' => fake()->userName(),
            'name' => fake()->name(),
            'address' => fake()->address(),
            'notes' => fake()->text(50),
            'role_type' => $role_type
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Role $user) {
            $parent_id = Role::select('role_id')->where('role_type', $user->role_type - 1)->get();
            $randnum = rand(0, $parent_id->count() - 1);
            $user->update(['parent_id' => $parent_id[$randnum]->role_id]);
            // ParentChild::create([
            //     'parent_id' => $parent_id[$randnum]->user_id,
            //     'child_id' => $user->user_id
            // ]);
        });
    }
}

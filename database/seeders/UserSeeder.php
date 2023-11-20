<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $saltstr = Str::random(10);
        $user_role = Role::where('code', 'admin')->first();
        \App\Models\User::create([
            'username' => 'admin123',
            'email' => 'admin123@email.com',
            'name' => 'Admin Admin',
            'salt' => $saltstr,
            'password' => bcrypt('admin123' . $saltstr),
            'notes' => 'This is Admin Account',
            'user_role_id' => $user_role->role_id,
            'phone_num' => '081188888888',
        ]);
        $roles = Role::get();
        foreach($roles as $rl){
            $saltstr = Str::random(10);
            $ug = $rl->user_groups()->first();
            \App\Models\User::factory(2)
            ->state([
                'salt' => $saltstr,
                'password' => bcrypt($ug->group_code . '123' . $saltstr),
                'user_role_id' => $rl->role_id,
            ])
            ->create();
        }
    }
}

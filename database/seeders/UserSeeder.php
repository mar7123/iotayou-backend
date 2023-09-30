<?php

namespace Database\Seeders;

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
        \App\Models\User::create([
            'username' => 'bigoof1928',
            'full_name' => 'Big Oof',
            'email' => 'bigoof.1928@email.com',
            'phone_num' => '081188888888',
            'pic' => 'Biggie',
            'address' => 'Saturnus Raya',
            'salt' => $saltstr,
            'password' => bcrypt('admin123' . $saltstr),
            'user_type' => 1
        ]);
        for ($i = 0; $i < 15; $i++) {
            \App\Models\User::factory(1)->create();
        }
    }
}

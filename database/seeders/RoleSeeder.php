<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Role::create([
            'code' => 'admin',
            'name' => 'Admin Admin',
            'address' => 'Saturnus Raya',
            'notes' => 'This is Admin',
            'role_type' => 1
        ]);
        for ($i = 0; $i < 10; $i++) {
            \App\Models\Role::factory(1)->create();
        }
    }
}

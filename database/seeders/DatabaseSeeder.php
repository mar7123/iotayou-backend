<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(UserGroupsSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(InstrumentSeeder::class);
        $this->call(SiteSeeder::class);
        $this->call(AlarmSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(AlertSeeder::class);
    }
}

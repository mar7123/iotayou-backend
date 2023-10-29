<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $adm = \App\Models\User::where('email', 'admin123@email.com')->first();
        // $user_group_id = [
        //     'admin.png',
        //     'prod_man.png',
        //     'main_man.png',
        //     'prod_sup.png',
        //     'prod_sup.png',
        //     'prod_sup.png',
        // ];
        // $page1st = [
        //     '/dashboard',
        //     '/dashboard',
        //     '/dashboard',
        //     '/dashboard',
        //     '/dashboard',
        //     '/dashboard',
        // ];
        // \App\Models\UserGroups::factory()
        //     ->count(count($user_group_id))
        //     ->sequence(fn ($sequence) => [
        //         'user_id' => $adm->user_id,
        //         'user_group_id' => $user_group_id[$sequence->index],
        //         'page1st' => $page1st[$sequence->index],
        //     ])
        //     ->create();
    }
}

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

        $user_group_id = \App\Models\UserGroups::orderBy('user_group_id')->get();
        // $user_group_id = [
        //     'admin.png',
        //     'prod_man.png',
        //     'main_man.png',
        //     'prod_sup.png',
        //     'prod_sup.png',
        //     'prod_sup.png',
        // ];
        $adm = \App\Models\User::where('email', 'admin123@email.com')->first();
        $adm_user_group = $user_group_id->where('user_group_id', '!=', $adm->role()->first()->role_type);
        foreach ($adm_user_group as $aug) {
            \App\Models\Permission::factory()
                ->count(1)
                ->state([
                    'user' => $adm->user_id,
                    'user_group' => $aug->user_group_id,
                    'user_permission' => 'vaed',
                ])
                ->create();
        }
        $users = \App\Models\User::where('email', '!=', 'admin123@email.com')->get();
        foreach ($users as $us) {
            $usr_group = $user_group_id->where('user_group_id', '>', $us->role()->first()->role_type);
            foreach ($usr_group as $ug) {
                \App\Models\Permission::factory()
                    ->count(1)
                    ->state([
                        'user' => $us->user_id,
                        'user_group' => $ug->user_group_id,
                    ])
                    ->create();
            }
        }
    }
}

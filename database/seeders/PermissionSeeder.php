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
        $adm_user_group = $user_group_id->where('user_group_id', '!=', $adm->user_type);
        foreach ($adm_user_group as $aug) {
            \App\Models\Permission::factory()
                ->count(1)
                ->state([
                    'user_id' => $adm->user_id,
                    'user_group_id' => $aug->user_group_id,
                    'user_permission' => 'vaed',
                ])
                ->create();
        }
        $usr = \App\Models\User::where('email', '!=', 'admin123@email.com')->get();
        foreach ($usr as $us) {
            $usr_group = $user_group_id->where('user_group_id', '>', $us->user_type);
            foreach ($usr_group as $ug) {
                \App\Models\Permission::factory()
                    ->count(1)
                    ->state([
                        'user_id' => $us->user_id,
                        'user_group_id' => $ug->user_group_id,
                    ])
                    ->create();
            }
        }
    }
}

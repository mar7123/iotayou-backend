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
        $adm = \App\Models\Role::where('code', 'admin')->first();
        $adm_user_group = $user_group_id->where('user_group_id', '!=', $adm->role_type);
        foreach ($adm_user_group as $aug) {
            \App\Models\Permission::factory()
                ->count(1)
                ->state([
                    'role' => $adm->role_id,
                    'user_group' => $aug->user_group_id,
                    'role_permission' => 'vaed',
                ])
                ->create();
        }
        $demo = \App\Models\Role::where('code', 'demo')->first();
        $dem_user_group = $user_group_id->where('user_group_id', '>', $demo->role_type);
        foreach ($dem_user_group as $dug) {
            \App\Models\Permission::factory()
                ->count(1)
                ->state([
                    'role' => $demo->role_id,
                    'user_group' => $dug->user_group_id,
                    'role_permission' => 'v---',
                ])
                ->create();
        }
        $roles = \App\Models\Role::where('code', '!=', 'admin')->get();
        foreach ($roles as $rl) {
            $usr_group = $user_group_id->where('user_group_id', '>', $rl->role_type);
            foreach ($usr_group as $ug) {
                \App\Models\Permission::factory()
                    ->count(1)
                    ->state([
                        'role' => $rl->role_id,
                        'user_group' => $ug->user_group_id,
                    ])
                    ->create();
            }
        }
    }
}

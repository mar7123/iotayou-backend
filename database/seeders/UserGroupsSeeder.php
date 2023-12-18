<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = [
            'Super Admin',
            'Client',
            'Customer',
            'Site',
            'Printer',
            'Alarm',
            'Instrument',
            'Parameter'
        ];
        $icon = [
            'admin.png',
            'prod_man.png',
            'main_man.png',
            'prod_sup.png',
            'prod_sup.png',
            'prod_sup.png',
            'prod_sup.png',
            'prod_sup.png',
        ];
        $page1st = [
            '/dashboard',
            '/dashboard',
            '/dashboard',
            '/dashboard',
            '/dashboard',
            '/dashboard',
            '/dashboard',
            '/dashboard',
        ];
        $group_code = [
            'sa',
            'cl',
            'cu',
            'si',
            'pr',
            'al',
            'ins',
            'par'
        ];
        \App\Models\UserGroups::factory()
            ->count(count($name))
            ->sequence(fn ($sequence) => [
                'name' => $name[$sequence->index],
                'icon' => $icon[$sequence->index],
                'page1st' => $page1st[$sequence->index],
                'group_code' => $group_code[$sequence->index],
            ])
            ->create();
    }
}

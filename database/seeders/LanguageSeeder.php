<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $langfunction = [
            'yesno',
            'yesno',
            null,
            null,
            null,
            'active',
            'active',
        ];
        $lang = [
            'yes',
            'no',
            'reserved',
            'reserved',
            'reserved',
            'active',
            'inactive',
        ];
        $badge = [
            'success',
            'danger',
            null,
            null,
            null,
            'success',
            'danger',
        ];
        \App\Models\Language::factory()
            ->count(count($langfunction))
            ->sequence(fn ($sequence) => [
                'langfunction' => $langfunction[$sequence->index],
                'lang' => $lang[$sequence->index],
                'badge' => $badge[$sequence->index],
            ])
            ->create();
    }
}

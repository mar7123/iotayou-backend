<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class SiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::where('user_type', 3)->get();
        foreach ($user as $cu) {
            \App\Models\Site::factory(2)
                ->state(new Sequence(
                    ['customer_id' => $cu->user_id]
                ))
                ->has(
                    \App\Models\Printer::factory((2))
                        ->state(new Sequence(
                            fn ($sequence) => [
                                'site_id' => function ($site) {
                                    dd($site);
                                    return $site['site_id'];
                                }
                            ]
                        ))
                        ->has(
                            \App\Models\Instrument::factory((1))
                                ->state(new Sequence(
                                    fn ($sequence) => [
                                        'printer_id' => function ($printer) {
                                            return $printer['printer_id'];
                                        }
                                    ]
                                )),
                            'instruments'
                        )
                        ->has(
                            \App\Models\Alarm::factory((2))
                                ->state(new Sequence(
                                    fn ($sequence) => [
                                        'printer_id' => function ($printer) {
                                            return $printer['printer_id'];
                                        }
                                    ]
                                )),
                            'alarms'
                        ),
                    'printers'
                )
                ->create();
        }
    }
}

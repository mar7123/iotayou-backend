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
        $roles = \App\Models\Role::where('role_type', 3)->get();
        $instruments = \App\Models\Instrument::get();
        foreach ($roles as $cu) {
            \App\Models\Site::factory(1)
                ->state(new Sequence(
                    ['customer_id' => $cu->role_id]
                ))
                ->has(
                    \App\Models\Printer::factory((2))
                        ->state(new Sequence(
                            fn ($sequence) => [
                                'site_id' => function ($site) {
                                    return $site['site_id'];
                                },
                                'instrument_id' => function ($site) use ($sequence, $instruments) {
                                    $ins = $instruments->get(($sequence->index % 5));
                                    return $ins->instrument_id;
                                }
                            ]
                        ))
                        // ->has(
                        //     \App\Models\Alarm::factory((2))
                        //         ->state(new Sequence(
                        //             fn ($sequence) => [
                        //                 'printer_id' => function ($printer) {
                        //                     return $printer['printer_id'];
                        //                 }
                        //             ]
                        //         )),
                        //     'alarms'
                        // )
                        ,
                    'printers'
                )
                ->create();
        }
    }
}

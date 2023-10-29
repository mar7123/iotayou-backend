<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class AlarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $printer = \App\Models\Printer::get();
        $par = \App\Models\Parameter::get();
        foreach ($printer as $pr) {
            foreach ($par as $pa) {
                if ($pr->instrument_id != $pa->instrument_id) {
                    continue;
                }
                \App\Models\Alarm::factory(1)
                    ->state(new Sequence(
                        fn ($sequence) => [
                            'printer_id' => function ($site) use ($pr) {
                                return $pr->printer_id;
                            },
                            'parameter_id' => function ($site) use ($pa) {
                                return $pa->parameter_id;
                            }
                        ]
                    ))
                    ->create();
            }
        }
    }
}

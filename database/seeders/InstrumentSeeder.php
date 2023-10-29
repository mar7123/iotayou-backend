<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class InstrumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Instrument::factory(5)
            ->has(
                \App\Models\Parameter::factory(2)
                    ->state(new Sequence(
                        fn ($sequence) => [
                            'instrument_id' => function ($instrument) {
                                return $instrument['instrument_id'];
                            },
                        ]
                    )),
                'parameters'
            )
            ->create();
    }
}

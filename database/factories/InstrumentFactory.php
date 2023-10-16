<?php

namespace Database\Factories;

use App\Models\Printer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instrument>
 */
class InstrumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = "D" . fake()->randomElement(["120", "320", "620"]) . "i";
        return [
            "code" => $code,
            "name" => "Domino Laser " . $code,
            "brand" => "Domino",
            "status" => 6,
        ];
    }
}

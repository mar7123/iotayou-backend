<?php

namespace Database\Factories;

use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Printer>
 */
class PrinterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "code" => fake()->firstName(),
            "name" => "Printer " . fake()->name(),
            "ip_addr" => "192.168." . fake()->numberBetween(0, 255) . "." . fake()->numberBetween(0, 255),
            "printer_port" => 2000,
            "status" => 6,
        ];
    }
}

<?php

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alert>
 */
class AlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $time = fake()->dateTimeBetween('-1 week', '-2 days');
        $timenow = $time->format('Y-m-d H:i:s');
        $status = fake()->numberBetween(1, 3);
        $solved_at = null;
        if ($status == 3) {
            $solved_at = $time->modify('+' . fake()->numberBetween(10, 20) . ' minutes');
            $solved_at = $solved_at->format('Y-m-d H:i:s');
        }
        return [
            "code" => fake()->firstName(),
            "name" => fake()->name(),
            "site_name" => fake()->name(),
            "printer_name" => fake()->name(),
            "status" => $status,
            "occured_at" => $timenow,
            "solved_at" => $solved_at
        ];
    }
}

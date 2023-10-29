<?php

namespace Database\Factories;

use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Alarm>
 */
class AlarmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $time = new DateTime();
        $timenow = $time->format('Y-m-d H:i:s');
        $status = fake()->numberBetween(1, 3);
        $solved_at = null;
        if($status == 3){
            $solved_at = $time->modify('+5 minutes');
            $solved_at = $solved_at->format('Y-m-d H:i:s');
        }
        return [
            "name" => fake()->sentence(2),
            "condition" => fake()->randomElement(['<', '>']) .  fake()->numberBetween(7, 15),
            "status" => $status,
            "occured_at" => $timenow,
            "solved_at" => $solved_at
        ];
    }
}

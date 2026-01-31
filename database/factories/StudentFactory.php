<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nim' => fake()->unique()->numerify('#########'),
            'full_name' => fake()->name(),
            'points_balance' => 0,
        ];
    }
}

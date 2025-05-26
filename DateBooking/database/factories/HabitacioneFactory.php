<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Habitacione>
 */
class HabitacioneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'tipo' => $this->faker->randomElement(['simple', 'doble', 'suite']),
            'numero' => $this->faker->numberBetween(1, 100),
            'capacidad' => $this->faker->numberBetween(1, 4),
        ];
    }
}

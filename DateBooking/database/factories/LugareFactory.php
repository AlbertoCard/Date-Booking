<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lugare>
 */
class LugareFactory extends Factory
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
            'fila' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'numero' => $this->faker->numberBetween(1, 100),
            'sector' => $this->faker->randomElement(['norte', 'sur', 'este', 'oeste']),
        ];
    }
}

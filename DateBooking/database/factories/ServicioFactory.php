<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Servicio>
 */
class ServicioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_establecimiento' => $this->faker->numberBetween(1, 10),
            'nombre' => $this->faker->word(),
            'descripcion' => $this->faker->sentence(),
            'costo' => $this->faker->randomFloat(2, 10, 100),
            'categoria' => $this->faker->randomElement(['restaurante', 'hotel', 'evento', 'consultorio']),
            'id_ciudad' => $this->faker->numberBetween(1, 100),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Establecimiento>
 */
class EstablecimientoFactory extends Factory
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
            'nombre' => $this->faker->company(),
            'telefono' => $this->faker->phoneNumber(),
            'direccion' => $this->faker->address(),
            'rfc' => $this->faker->unique()->regexify('[A-Z0-9]{13}'),
            'estado' => $this->faker->randomElement(['activo', 'inactivo']),
            'codigo_postal' => $this->faker->postcode(),
            'pais' => $this->faker->country(),
            'stripe_account_id'=> "",  
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EstabXUsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //tomar uid de usuario y el id de establecimiento
            'usuario_id' => \App\Models\Usuario::factory(),
            'establecimiento_id' => \App\Models\Establecimiento::factory(),
        ];
    }
}

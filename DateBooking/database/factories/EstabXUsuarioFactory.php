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
        $usuario = \App\Models\Usuario::factory()->create();

        return [
            'id_usuario' => $usuario->uid,
            'id_establecimiento' => \App\Models\Establecimiento::factory()->create()->id,
        ];
    }
}

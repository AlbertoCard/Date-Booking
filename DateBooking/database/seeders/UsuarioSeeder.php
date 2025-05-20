<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Usuario::created([
            'uid' => 'A3rWEA1Mq1U6kHFBvSYhz3otH633',
            'nombre' => 'Luis Serrano',
            'email' => 'serranob08@outlook.es',
            'telefono' => '6671791711',
            'foto_url' => 'https://via.placeholder.com/150',
            'rol' => 'cliente',
            'activo' => 0,
        ]);

        Usuario::created([
            'uid' => 'LZcyWClVmQUWwoXnfziqMvNHVjy1',
            'nombre' => 'Adolfo Valdez',
            'email' => 'adolfo.valdez260@gmail.com',
            'telefono' => '6671791711',
            'foto_url' => 'https://via.placeholder.com/150',
            'rol' => 'cliente',
            'activo' => 0,
        ]);
        Usuario::created([
            'uid' => 'yeZuUjbQVid28DTBqGmbmYvHqfH2',
            'nombre' => 'Alberto Cardenas',
            'email' => 'alberto.cardenaszazueta@gmail.com',
            'telefono' => '6671791711',
            'foto_url' => 'https://via.placeholder.com/150',
            'rol' => 'cliente',
            'activo' => 0,
        ]);
        Usuario::created([
            'uid' => 'zggJkZIFbIN9ftoYgsCbRJjd8Er1',
            'nombre' => 'Ramses Sonso',
            'email' => 'ramchudios@gmail.com',
            'telefono' => '6671791711',
            'foto_url' => 'https://via.placeholder.com/150',
            'rol' => 'cliente',
            'activo' => 0,
        ]);
    }
}

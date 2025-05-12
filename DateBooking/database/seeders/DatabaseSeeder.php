<?php

namespace Database\Seeders;

use App\Models\Disponibilidad;
use App\Models\Establecimiento;
use App\Models\EstabXusuario;
use App\Models\Habitacione;
use App\Models\Lugare;
use App\Models\Medico;
use App\Models\Mesa;
use App\Models\Servicio;
use App\Models\User;
use App\Models\Usuario;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Illuminate\Support\enum_value;
use function PHPUnit\Framework\isEmpty;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        EstabXusuario::factory(10)->create();
        Servicio::factory(20)->create();

        //asignar disponibilidad a los servicios
        $hoteles = Servicio::where('categoria', 'hotel')->get();
        $eventos = Servicio::where('categoria', 'evento')->get();
        $consultorios = Servicio::where('categoria', 'consultorio')->get();
        $restaurantes = Servicio::where('categoria', 'restaurante')->get();

        foreach ($hoteles as $hotel) {
            Disponibilidad::factory(5)->create([
                'id_servicio' => $hotel->id_servicio,
                'fecha' => null,
                'hora_inicio' => now()->addHours(rand(1, 12)),
                'hora_fin' => now()->addHours(rand(13, 24)),
                //valor time
                'intervalo' => "00:00:00",
                //valor enum
                'dias' => enum_value('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'),
                'tipo' => 'recurrente',
                'activo' => 1,
            ]);
            Habitacione::factory(5)->create([
                'id_servicio' => $hotel->id_servicio,
            ]);
        }

        foreach ($eventos as $evento) {
            Disponibilidad::factory(5)->create([
                'id_servicio' => $evento->id_servicio,
                'fecha' => now()->addDays(rand(1, 30)),
                'hora_inicio' => now()->addHours(rand(1, 12)),
                'hora_fin' => now()->addHours(rand(1, 12)),
                'intervalo' => "00:00:00",
                'dias' => enum_value('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'),
                'tipo' => 'unica',
                'activo' => 1,
            ]);
            Lugare::factory(5)->create([
                'id_servicio' => $evento->id_servicio,
            ]);
        }

        foreach ($consultorios as $consultorio) {
            Disponibilidad::factory(5)->create([
                'id_servicio' => $consultorio->id_servicio,
                'fecha' => null,
                'hora_inicio' => now()->addHours(rand(1, 12)),
                'hora_fin' => now()->addHours(rand(13, 24)),
                //valor time
                'intervalo' => "00:20:00",
                //valor enum
                'dias' => enum_value('lunes', 'martes', 'miercoles', 'jueves', 'viernes'),
                'tipo' => 'recurrente',
                'activo' => 1,
            ]);
            Medico::factory(5)->create([
                'id_servicio' => $consultorio->id_servicio,
            ]);
        }

        foreach ($restaurantes as $restaurante) {
            Disponibilidad::factory(5)->create([
                'id_servicio' => $restaurante->id_servicio,
                'fecha' => null,
                'hora_inicio' => now()->addHours(rand(8, 12)),
                'hora_fin' => now()->addHours(rand(13, 20)),
                //valor time
                'intervalo' => "02:00:00",
                //valor enum
                'dias' => enum_value('lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'),
                'tipo' => 'recurrente',
                'activo' => 1,
            ]);
            Mesa::factory(5)->create([
                'id_servicio' => $restaurante->id_servicio,
            ]);
        }

        
    }
}

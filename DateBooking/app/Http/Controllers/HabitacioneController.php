<?php

namespace App\Http\Controllers;

use App\Models\Habitacione;
use Illuminate\Support\Facades\Log;

class HabitacioneController extends Controller
{
    public function getByServicio($id_servicio)
    {
        try {
            Log::info("Buscando habitaciones para el servicio con ID: {$id_servicio}");

            $habitaciones = Habitacione::where('id_servicio', $id_servicio)->get();

            Log::info("Habitaciones encontradas: " . $habitaciones->count());

            return response()->json($habitaciones);
        } catch (\Exception $e) {
            Log::error("Error al obtener las habitaciones para el servicio {$id_servicio}: " . $e->getMessage());

            return response()->json([
                'message' => 'Error al obtener las habitaciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

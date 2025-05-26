<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Support\Facades\Log;

class MedicoController extends Controller
{
    public function getByServicio($id_servicio)
    {
        try {
            Log::info("Buscando médicos para el servicio ID: {$id_servicio}");
            $medicos = Medico::where('id_servicio', $id_servicio)->get();
            Log::info("Médicos encontrados: " . $medicos->count());
            return response()->json($medicos);
        } catch (\Exception $e) {
            Log::error("Error al obtener los médicos para el servicio ID {$id_servicio}: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al obtener los médicos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

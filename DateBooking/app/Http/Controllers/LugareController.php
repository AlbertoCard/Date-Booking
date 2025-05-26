<?php

namespace App\Http\Controllers;

use App\Models\Lugare;
use Illuminate\Support\Facades\Log;

class LugareController extends Controller
{
    public function getByServicio($id_servicio)
    {
        try {
            Log::info("Obteniendo lugares para el servicio con ID: {$id_servicio}");

            $lugares = Lugare::where('id_servicio', $id_servicio)
                ->orderBy('sector')
                ->orderBy('fila')
                ->orderBy('numero')
                ->get();

            Log::info("Lugares obtenidos correctamente", ['cantidad' => $lugares->count()]);

            return response()->json($lugares);
        } catch (\Exception $e) {
            Log::error("Error al obtener los lugares", [
                'id_servicio' => $id_servicio,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error al obtener los lugares',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

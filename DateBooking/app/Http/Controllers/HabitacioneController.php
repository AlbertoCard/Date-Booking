<?php

namespace App\Http\Controllers;

use App\Models\Habitacione;
use Illuminate\Http\Request;

class HabitacioneController extends Controller
{
    public function getByServicio($id_servicio)
    {
        try {
            $habitaciones = Habitacione::where('id_servicio', $id_servicio)->get();
            
            return response()->json($habitaciones);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las habitaciones',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
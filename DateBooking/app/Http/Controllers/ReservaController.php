<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    //

    function obtenerReservas($id)
    {
        try {
            $reservas = Reserva::with(['servicio:id_servicio,nombre'])
                ->where('id_usuario', $id)
                ->get();

            return response()->json(['reservas' => $reservas], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener reservas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

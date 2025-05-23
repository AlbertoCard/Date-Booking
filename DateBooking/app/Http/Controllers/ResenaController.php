<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reseña;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;

class ResenaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_reserva' => 'required|exists:reservas,id_reserva',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|max:1000',
            'user_uid' => 'required|string'
        ]);

        $reserva = Reserva::findOrFail($request->id_reserva);

        // Validar que el usuario sea el dueño de la reserva usando el UID recibido
        if ($reserva->id_usuario !== $request->user_uid) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $resena = new Reseña();
        $resena->id_usuario = $reserva->id_usuario;
        $resena->id_servicio = $reserva->id_servicio;
        $resena->calificacion = $request->calificacion;
        $resena->descripcion = $request->comentario;
        $resena->fecha = now();
        $resena->save();

        return response()->json(['message' => 'Reseña guardada correctamente'], 201);
    }
} 
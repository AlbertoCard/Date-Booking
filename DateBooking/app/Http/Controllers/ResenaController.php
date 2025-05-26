<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reseña;
use App\Models\Reserva;
use Illuminate\Support\Facades\Log;

class ResenaController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Iniciando proceso de guardado de reseña', [
            'input' => $request->all()
        ]);

        try {
            $request->validate([
                'id_reserva' => 'required|exists:reservas,id_reserva',
                'calificacion' => 'required|integer|min:1|max:5',
                'comentario' => 'required|string|max:1000',
                'user_uid' => 'required|string'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validación fallida en ResenaController@store', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            return response()->json(['message' => 'Datos inválidos', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error inesperado durante la validación en ResenaController@store', [
                'exception' => $e->getMessage(),
                'input' => $request->all()
            ]);
            return response()->json(['message' => 'Error inesperado durante la validación'], 500);
        }

        try {
            $reserva = Reserva::findOrFail($request->id_reserva);
        } catch (\Exception $e) {
            Log::error('Reserva no encontrada en ResenaController@store', [
                'id_reserva' => $request->id_reserva,
                'exception' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Reserva no encontrada'], 404);
        }

        // Validar que el usuario sea el dueño de la reserva usando el UID recibido
        if ($reserva->id_usuario !== $request->user_uid) {
            Log::warning('Intento de acceso no autorizado en ResenaController@store', [
                'id_usuario' => $reserva->id_usuario,
                'user_uid' => $request->user_uid
            ]);
            return response()->json(['message' => 'No autorizado'], 403);
        }

        try {
            $resena = new Reseña();
            $resena->id_usuario = $reserva->id_usuario;
            $resena->id_servicio = $reserva->id_servicio;
            $resena->calificacion = $request->calificacion;
            $resena->descripcion = $request->comentario;
            $resena->fecha = now();
            $resena->save();
            Log::info('Reseña guardada exitosamente', [
                'id_resena' => $resena->id ?? null,
                'input' => $request->all()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al guardar la reseña en ResenaController@store', [
                'exception' => $e->getMessage(),
                'input' => $request->all()
            ]);
            return response()->json(['message' => 'Error al guardar la reseña'], 500);
        }

        return response()->json(['message' => 'Reseña guardada correctamente'], 201);
    }

    public function getByServicio($id_servicio)
    {
        Log::info('Obteniendo reseñas para servicio', [
            'id_servicio' => $id_servicio
        ]);

        try {
            $reseñas = Reseña::with('usuario')
                ->where('id_servicio', $id_servicio)
                ->orderBy('fecha', 'desc')
                ->get();
            Log::info('Reseñas obtenidas correctamente', [
                'id_servicio' => $id_servicio,
                'cantidad' => $reseñas->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener reseñas en ResenaController@getByServicio', [
                'id_servicio' => $id_servicio,
                'exception' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Error al obtener reseñas'], 500);
        }

        return response()->json($reseñas);
    }
}

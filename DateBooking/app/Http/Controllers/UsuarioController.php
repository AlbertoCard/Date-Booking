<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UsuarioController extends Controller
{
    /**
     * Obtener todos los usuarios
     */
    public function index()
    {
        try {
            $usuarios = Usuario::all();
            return response()->json(['usuarios' => $usuarios]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda un nuevo usuario desde Firebase
     */
    public function store(Request $request)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string|max:128',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'string|max:20',
            'foto_url' => 'required|string',
            'rol' => 'string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Crear el usuario con los datos validados
            $usuario = Usuario::create([
                'uid' => $request->uid,
                'nombre' => $request->nombre,
                'email' => $request->email,
                'telefono' => $request->telefono ?? '0000000000',
                'foto_url' => $request->foto_url,
                'rol' => $request->rol ?? 'cliente',
                'fecha_creacion' => now(),
                'activo' => 1
            ]);

            return response()->json([
                'message' => 'Usuario creado exitosamente',
                'usuario' => $usuario
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualiza el estado activo del usuario
     */
    public function updateActivo(Request $request, $uid)
    {
        Log::info('Intentando actualizar estado de usuario', ['uid' => $uid]);

        try {
            $usuario = Usuario::where('uid', $uid)->first();
            
            if (!$usuario) {
                Log::error('Usuario no encontrado', ['uid' => $uid]);
                return response()->json([
                    'message' => 'Usuario no encontrado',
                    'uid' => $uid
                ], 404);
            }

            Log::info('Usuario encontrado', ['usuario' => $usuario->toArray()]);

            $usuario->activo = 0;
            $usuario->save();

            Log::info('Estado de usuario actualizado correctamente', [
                'uid' => $uid,
                'activo' => $usuario->activo
            ]);

            return response()->json([
                'message' => 'Estado actualizado correctamente',
                'usuario' => $usuario
            ]);

        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de usuario', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al actualizar el estado del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activa un usuario cuando inicia sesión
     */
    public function activarUsuario(Request $request, $uid)
    {
        Log::info('Intentando activar usuario', ['uid' => $uid]);

        try {
            $usuario = Usuario::where('uid', $uid)->first();
            
            if (!$usuario) {
                Log::error('Usuario no encontrado para activar', ['uid' => $uid]);
                return response()->json([
                    'message' => 'Usuario no encontrado',
                    'uid' => $uid
                ], 404);
            }

            $usuario->activo = 1;
            $usuario->save();

            Log::info('Usuario activado correctamente', [
                'uid' => $uid,
                'activo' => $usuario->activo
            ]);

            return response()->json([
                'message' => 'Usuario activado correctamente',
                'usuario' => $usuario
            ]);

        } catch (\Exception $e) {
            Log::error('Error al activar usuario', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al activar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function obtenerReservas($id)
    {
        try {
            $usuario = Usuario::with('reservas')
                ->where('uid', $id)
                ->first();
            if (!$usuario) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
            $reservas = $usuario->reservas;
            if ($reservas->isEmpty()) {
                return response()->json(['message' => 'No hay reservas para este usuario'], 404);
            }
            return response()->json(['reservas' => $reservas], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener reservas', 'error' => $e->getMessage()], 500);
        }
    }
}

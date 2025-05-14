<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
            Log::error('Error al obtener usuarios', ['error' => $e->getMessage()]);
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
        Log::info('Intentando crear usuario', ['datos' => $request->all()]);

        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string|max:128',
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'foto_url' => 'required|string',
            'rol' => 'required|string|in:cliente,establecimiento'
        ]);

        if ($validator->fails()) {
            Log::error('Error de validación al crear usuario', ['errores' => $validator->errors()]);
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Preparar los datos para crear el usuario
            $userData = [
                'uid' => $request->uid,
                'nombre' => $request->nombre,
                'email' => $request->email,
                'telefono' => $request->telefono ?? '0000000000',
                'foto_url' => $request->foto_url,
                'rol' => $request->rol,
                'activo' => 1
            ];

            Log::info('Datos preparados para crear usuario', ['userData' => $userData]);

            // Crear el usuario
            $usuario = Usuario::create($userData);

            DB::commit();

            Log::info('Usuario creado exitosamente', ['usuario' => $usuario]);

            return response()->json([
                'message' => 'Usuario creado exitosamente',
                'usuario' => $usuario
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear usuario', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'datos' => $request->all()
            ]);

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

    /**
     * Obtener un usuario específico por su UID
     */
    public function show($uid)
    {
        try {
            $usuario = Usuario::where('uid', $uid)->first();
            
            if (!$usuario) {
                return response()->json([
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            return response()->json([
                'usuario' => $usuario
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener usuario', [
                'uid' => $uid,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'message' => 'Error al obtener el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

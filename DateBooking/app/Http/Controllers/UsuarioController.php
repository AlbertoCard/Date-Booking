<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
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
}

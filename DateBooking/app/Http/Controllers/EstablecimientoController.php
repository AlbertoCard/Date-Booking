<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use App\Models\estbXusuario;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EstablecimientoController extends Controller
{
    /**
     * Obtener todos los establecimientos
     */
    public function index()
    {
        try {
            $establecimientos = Establecimiento::with('usuarios')->get();
            return response()->json(['establecimientos' => $establecimientos]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener establecimientos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda un nuevo establecimiento
     */
    public function store(Request $request)
    {
        Log::info('Intentando crear establecimiento', ['datos' => $request->all()]);

        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string',
            'rfc' => 'nullable|string|max:50',
            'estado' => 'nullable|string|max:10',
            'stripe_account_id' => 'nullable|string',
            'id_usuario' => 'required|string|max:128'
        ]);

        if ($validator->fails()) {
            Log::error('Error de validación al crear establecimiento', ['errores' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Verificar si el usuario existe
            $usuario = Usuario::where('uid', $request->id_usuario)->first();
            
            if (!$usuario) {
                return response()->json([
                    'message' => 'Error al crear el establecimiento',
                    'error' => 'El usuario especificado no existe en la base de datos'
                ], 404);
            }

            DB::beginTransaction();

            // Crear el establecimiento
            $establecimientoData = [
                'nombre' => $request->nombre,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion ?? 'Sin dirección',
                'rfc' => $request->rfc ?? 'Sin RFC',
                'estado' => $request->estado ?? 'Activo',
                'stripe_account_id' => $request->stripe_account_id ?? 'Sin cuenta'
            ];

            Log::info('Datos preparados para crear establecimiento', ['establecimientoData' => $establecimientoData]);

            $establecimiento = Establecimiento::create($establecimientoData);

            Log::info('Establecimiento creado', ['establecimiento' => $establecimiento]);

            // Crear la relación en estb_xusuario
            $relacion = estbXusuario::create([
                'id_usuario' => $usuario->uid, // Usar el uid del usuario verificado
                'id_establecimiento' => $establecimiento->id_establecimiento
            ]);

            Log::info('Relación usuario-establecimiento creada', ['relacion' => $relacion]);

            DB::commit();

            return response()->json([
                'message' => 'Establecimiento creado exitosamente',
                'establecimiento' => $establecimiento
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error al crear establecimiento', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'message' => 'Error al crear el establecimiento',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Obtener establecimientos por usuario
     */
    public function getByUsuario($uid)
    {
        try {
            $establecimientos = Establecimiento::whereHas('usuarios', function($query) use ($uid) {
                $query->where('id_usuario', $uid);
            })->get();

            return response()->json(['establecimientos' => $establecimientos]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener establecimientos del usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un establecimiento
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255',
            'telefono' => 'string|max:20',
            'direccion' => 'string',
            'rfc' => 'string|max:50',
            'estado' => 'string|max:10',
            'codigo_postal' => 'string|max:10',
            'pais' => 'string|max:100',
            'id_estado' => 'integer',
            'stripe_account_id' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $establecimiento = Establecimiento::findOrFail($id);
            $establecimiento->update($request->all());

            return response()->json([
                'message' => 'Establecimiento actualizado exitosamente',
                'establecimiento' => $establecimiento
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el establecimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
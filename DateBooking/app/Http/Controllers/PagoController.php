<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Log de los datos recibidos
            Log::info('Datos recibidos en PagoController:', $request->all());

            // Verificar si el usuario existe
            $usuario = Usuario::where('uid', $request->id_usuario)->first();
            if (!$usuario) {
                return response()->json([
                    'message' => 'El usuario no existe en la base de datos',
                    'error' => 'Usuario no encontrado'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required|string',
                'monto' => 'required|numeric|min:0',
                'metodo_pago' => 'required|string',
                'estado_pago' => 'required|string',
                'moneda' => 'required|string|size:3'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Preparar los datos para la creación
            $pagoData = [
                'id_usuario' => $request->id_usuario,
                'monto' => (float) $request->monto,
                'metodo_pago' => $request->metodo_pago,
                'estado_pago' => $request->estado_pago,
                'moneda' => $request->moneda,
                'stripe_payment_intent_id' => '',
                'stripe_customer_id' => '',
                'fecha_pago' => now()
            ];

            // Log de los datos a crear
            Log::info('Datos a crear en la tabla pagos:', $pagoData);

            $pago = Pago::create($pagoData);
            
            DB::commit();
            
            return response()->json([
                'message' => 'Pago creado exitosamente',
                'id_pago' => $pago->id_pago
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear pago:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error al crear el pago',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $pago = Pago::findOrFail($id);
            return response()->json($pago);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Pago no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }
} 
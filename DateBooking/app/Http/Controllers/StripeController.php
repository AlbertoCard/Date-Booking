<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            Log::info('Iniciando checkout de Stripe:', $request->all());

            // Validar los datos de entrada
            $validator = Validator::make($request->all(), [
                'userId' => 'required|string',
                'reservaId' => 'required|integer',
                'monto' => 'required|numeric|min:1'
            ]);

            if ($validator->fails()) {
                Log::error('Error de validación en checkout:', $validator->errors()->toArray());
                return response()->json([
                    'error' => 'Datos de entrada inválidos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar si la clave de Stripe está configurada
            $stripeKey = config('services.stripe.secret');
            Log::info('Verificando configuración de Stripe:', [
                'key_exists' => !empty($stripeKey),
                'key_length' => strlen($stripeKey),
                'key_prefix' => substr($stripeKey, 0, 7),
                'app_url' => config('app.url')
            ]);
            
            if (empty($stripeKey)) {
                Log::error('La clave secreta de Stripe no está configurada');
                return response()->json(['error' => 'Configuración de Stripe incompleta'], 500);
            }

            // Verificar si el usuario está autenticado
            if (!$request->has('userId')) {
                Log::error('Usuario no autenticado');
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            Log::info('Usuario autenticado:', ['userId' => $request->userId]);

            try {
                // Configurar la clave secreta de Stripe
                Stripe::setApiKey($stripeKey);
                Log::info('Clave de Stripe configurada correctamente');
            } catch (\Exception $e) {
                Log::error('Error al configurar la clave de Stripe:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['error' => 'Error al configurar Stripe'], 500);
            }

            // Obtener el monto de la reserva y validar
            $monto = floatval($request->monto);
            if ($monto <= 0) {
                Log::error('Monto inválido:', ['monto' => $monto]);
                return response()->json(['error' => 'El monto debe ser mayor a 0'], 422);
            }

            // Convertir el monto a centavos para Stripe
            $montoEnCentavos = (int)($monto * 100);
            Log::info('Monto procesado:', [
                'monto_original' => $monto,
                'monto_en_centavos' => $montoEnCentavos
            ]);

            $reservaId = $request->reservaId;

            // Obtener el tipo de servicio de la reserva
            $reserva = \App\Models\Reserva::find($reservaId);
            if (!$reserva) {
                Log::error('Reserva no encontrada:', ['reservaId' => $reservaId]);
                return response()->json(['error' => 'Reserva no encontrada'], 404);
            }

            $tipoServicio = $reserva->tipo_servicio;
            Log::info('Datos de la reserva:', [
                'reserva_id' => $reservaId,
                'tipo_servicio' => $tipoServicio,
                'monto' => $monto,
                'monto_en_centavos' => $montoEnCentavos
            ]);

            try {
                // Crear la sesión de checkout
                $session = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'mxn',
                            'product_data' => [
                                'name' => 'Reservación de ' . ucfirst($tipoServicio),
                                'description' => 'Reserva #' . $reservaId,
                            ],
                            'unit_amount' => $montoEnCentavos,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => config('app.url') . '/api/stripe/success?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => config('app.url') . '/reservas/' . $request->userId . '?canceled=true',
                    'client_reference_id' => $request->userId,
                    'metadata' => [
                        'reserva_id' => $reservaId,
                        'monto_total' => $monto
                    ]
                ]);

                Log::info('Sesión de Stripe creada exitosamente:', [
                    'session_id' => $session->id,
                    'monto_total' => $monto,
                    'monto_en_centavos' => $montoEnCentavos
                ]);

                return response()->json(['id' => $session->id]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                Log::error('Error de API de Stripe:', [
                    'error' => $e->getMessage(),
                    'type' => $e->getStripeCode(),
                    'http_status' => $e->getHttpStatus(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Error al procesar el pago con Stripe',
                    'message' => $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error en Stripe checkout:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error al procesar el pago',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function success()
    {
        try {
            // Configurar la clave de Stripe
            $stripeKey = config('services.stripe.secret');
            if (empty($stripeKey)) {
                Log::error('La clave secreta de Stripe no está configurada en success');
                return redirect('/reservas?error=configuracion');
            }
            Stripe::setApiKey($stripeKey);

            // Verificar que tenemos el session_id
            if (!isset($_GET['session_id'])) {
                Log::error('No se recibió session_id en success');
                return redirect('/reservas?error=sesion');
            }

            Log::info('Procesando pago exitoso:', ['session_id' => $_GET['session_id']]);

            // Obtener el ID de la reserva de la sesión de Stripe
            $session = Session::retrieve($_GET['session_id']);
            Log::info('Sesión recuperada:', ['session' => $session]);

            if (!isset($session->metadata->reserva_id)) {
                Log::error('No se encontró reserva_id en los metadatos de la sesión');
                return redirect('/reservas?error=reserva');
            }

            $reservaId = $session->metadata->reserva_id;
            $userId = $session->client_reference_id; // Obtener el ID del usuario
            Log::info('ID de reserva y usuario encontrados:', ['reserva_id' => $reservaId, 'user_id' => $userId]);

            // Actualizar el estado de la reserva
            $reserva = \App\Models\Reserva::find($reservaId);
            if (!$reserva) {
                Log::error('No se encontró la reserva en la base de datos', ['reserva_id' => $reservaId]);
                return redirect('/reservas?error=reserva_no_encontrada');
            }

            $reserva->estado = 'pendiente';
            $reserva->save();
            Log::info('Reserva actualizada exitosamente', ['reserva_id' => $reservaId]);

            // Redirigir al usuario a sus reservas con mensaje de éxito
            return redirect("/reservas/{$userId}?success=true");

        } catch (\Exception $e) {
            Log::error('Error en success:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect('/reservas?error=proceso');
        }
    }

    public function cancel()
    {
        return response()->json(['message' => 'Pago cancelado']);
    }
}
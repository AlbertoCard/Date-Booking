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
            if (empty($stripeKey)) {
                Log::error('La clave secreta de Stripe no está configurada');
                return response()->json(['error' => 'Configuración de Stripe incompleta'], 500);
            }

            // Verificar si el usuario está autenticado
            if (!$request->has('userId')) {
                Log::error('Usuario no autenticado');
                return response()->json(['error' => 'Usuario no autenticado'], 401);
            }

            // Configurar la clave secreta de Stripe
            try {
                Stripe::setApiKey($stripeKey);
            } catch (\Exception $e) {
                Log::error('Error al configurar la clave de Stripe:', [
                    'message' => $e->getMessage()
                ]);
                return response()->json(['error' => 'Error de configuración de Stripe'], 500);
            }

            // Obtener el monto de la reserva y validar
            $monto = floatval($request->monto);
            if ($monto <= 0) {
                Log::error('Monto inválido:', ['monto' => $monto]);
                return response()->json(['error' => 'El monto debe ser mayor a 0'], 422);
            }

            // Convertir el monto a centavos para Stripe
            $montoEnCentavos = (int)($monto * 100);

            $reservaId = $request->reservaId;

            // Obtener el tipo de servicio de la reserva
            try {
                $reserva = \App\Models\Reserva::find($reservaId);
            } catch (\Exception $e) {
                Log::error('Error al buscar la reserva:', [
                    'reservaId' => $reservaId,
                    'message' => $e->getMessage()
                ]);
                return response()->json(['error' => 'Error al buscar la reserva'], 500);
            }

            if (!$reserva) {
                Log::error('Reserva no encontrada:', ['reservaId' => $reservaId]);
                return response()->json(['error' => 'Reserva no encontrada'], 404);
            }

            try {
                // Crear la sesión de checkout
                $session = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => 'mxn',
                            'product_data' => [
                                'name' => 'Reservación de ' . ucfirst($reserva->tipo_servicio),
                                'description' => 'Reserva #' . $reservaId,
                            ],
                            'unit_amount' => $montoEnCentavos,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'success_url' => url('/api/stripe/success?session_id={CHECKOUT_SESSION_ID}'),
                    'cancel_url' => url('/reservas/' . $request->userId . '?canceled=true'),

                    'client_reference_id' => $request->userId,
                    'metadata' => [
                        'reserva_id' => $reservaId,
                        'monto_total' => $monto
                    ]
                ]);

                Log::info('Sesión de Stripe creada exitosamente:', [
                    'session_id' => $session->id
                ]);

                return response()->json(['id' => $session->id]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                Log::error('Error de API de Stripe:', [
                    'error' => $e->getMessage(),
                    'type' => $e->getStripeCode(),
                    'http_status' => $e->getHttpStatus()
                ]);
                return response()->json([
                    'error' => 'Error al procesar el pago con Stripe',
                    'message' => $e->getMessage()
                ], 500);
            } catch (\Exception $e) {
                Log::error('Error general al crear la sesión de Stripe:', [
                    'message' => $e->getMessage()
                ]);
                return response()->json([
                    'error' => 'Error inesperado al crear la sesión de Stripe',
                    'message' => $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error en Stripe checkout:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
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
            Log::info('Iniciando proceso de success con session_id:', ['session_id' => $_GET['session_id'] ?? 'no session_id']);

            // Configurar la clave de Stripe
            $stripeKey = config('services.stripe.secret');
            if (empty($stripeKey)) {
                Log::error('La clave secreta de Stripe no está configurada en success');
                return redirect(url('/reservas?error=configuracion'));
            }
            try {
                Stripe::setApiKey($stripeKey);
            } catch (\Exception $e) {
                Log::error('Error al configurar la clave de Stripe en success:', [
                    'message' => $e->getMessage()
                ]);
                return redirect(url('/reservas?error=configuracion_stripe'));
            }

            // Verificar que tenemos el session_id
            if (!isset($_GET['session_id'])) {
                Log::error('No se recibió session_id en success');
                return redirect(url('/reservas?error=sesion'));
            }

            try {
                // Obtener la sesión de Stripe
                $session = Session::retrieve($_GET['session_id']);
                Log::info('Sesión recuperada:', ['session' => $session]);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                Log::error('Error al recuperar la sesión de Stripe:', [
                    'error' => $e->getMessage(),
                    'type' => $e->getStripeCode()
                ]);
                return redirect(url('/reservas?error=stripe_session'));
            } catch (\Exception $e) {
                Log::error('Error general al recuperar la sesión de Stripe:', [
                    'message' => $e->getMessage()
                ]);
                return redirect(url('/reservas?error=stripe_session_general'));
            }

            // Obtener los IDs de los metadatos
            if (!isset($session->metadata->reserva_id)) {
                Log::error('Metadatos incompletos en la sesión:', [
                    'reserva_id_exists' => isset($session->metadata->reserva_id),
                    'metadata' => $session->metadata
                ]);
                return redirect(url('/reservas?error=metadatos'));
            }

            $reservaId = $session->metadata->reserva_id;
            $userId = $session->client_reference_id;
            $monto = $session->metadata->monto_total;

            Log::info('IDs recuperados:', [
                'reserva_id' => $reservaId,
                'user_id' => $userId,
                'monto' => $monto
            ]);

            try {
                // Crear el registro de pago
                $pago = \App\Models\Pago::create([
                    'id_usuario' => $userId,
                    'id_reserva' => $reservaId,
                    'monto' => $monto,
                    'metodo_pago' => 'stripe',
                    'estado_pago' => 'completado',
                    'moneda' => 'MXN',
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'stripe_customer_id' => $session->customer ?? '',
                    'fecha_pago' => now()
                ]);

                Log::info('Pago creado exitosamente:', [
                    'pago_id' => $pago->id_pago,
                    'payment_intent' => $session->payment_intent
                ]);
            } catch (\Exception $e) {
                Log::error('Error al crear el pago:', [
                    'error' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect(url('/reservas?error=creacion_pago'));
            }

            // Actualizar el estado de la reserva
            try {
                $reserva = \App\Models\Reserva::find($reservaId);
            } catch (\Exception $e) {
                Log::error('Error al buscar la reserva en success:', [
                    'reserva_id' => $reservaId,
                    'message' => $e->getMessage()
                ]);
                return redirect(url('/reservas?error=buscar_reserva'));
            }

            if (!$reserva) {
                Log::error('No se encontró la reserva:', ['reserva_id' => $reservaId]);
                return redirect(url('/reservas?error=reserva_no_encontrada'));
            }

            try {
                $estadoAnterior = $reserva->estado;
                $reserva->estado = 'pendiente';
                $reserva->save();

                Log::info('Reserva actualizada exitosamente:', [
                    'reserva_id' => $reservaId,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => 'pendiente'
                ]);
            } catch (\Exception $e) {
                Log::error('Error al actualizar la reserva:', [
                    'error' => $e->getMessage(),
                    'reserva_id' => $reservaId
                ]);
                return redirect(url('/reservas?error=actualizacion_reserva'));
            }

            // Redirigir al usuario a sus reservas
            return redirect(url("/reservas/{$userId}?success=true"));
        } catch (\Exception $e) {
            Log::error('Error en success:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect(url('/reservas?error=proceso'));
        }
    }

    public function cancel()
    {
        Log::info('Pago cancelado por el usuario');
        return response()->json(['message' => 'Pago cancelado']);
    }
}

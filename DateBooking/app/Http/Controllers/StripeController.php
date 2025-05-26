<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            // Verificar si la clave de Stripe está configurada
            $stripeKey = config('services.stripe.secret');
            Log::info('Verificando configuración de Stripe:', [
                'key_exists' => !empty($stripeKey),
                'key_length' => strlen($stripeKey),
                'key_prefix' => substr($stripeKey, 0, 7)
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

            // Configurar la clave secreta de Stripe
            Stripe::setApiKey($stripeKey);

            Log::info('Iniciando creación de sesión de Stripe');

            // Obtener el monto de la reserva
            $monto = $request->monto ?? 5000; // Por defecto $50.00 USD si no se especifica
            $reservaId = $request->reservaId;

            // Obtener el tipo de servicio de la reserva
            $reserva = \App\Models\Reserva::find($reservaId);
            $tipoServicio = $reserva ? $reserva->tipo_servicio : 'servicio';

            // Crear la sesión de checkout
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'mxn', // Cambiado a pesos mexicanos
                        'product_data' => [
                            'name' => 'Reservación de ' . ucfirst($tipoServicio),
                            'description' => 'Reserva #' . $reservaId,
                        ],
                        'unit_amount' => $monto * 100, // Stripe usa centavos
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://127.0.0.1:8000/api/stripe/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://127.0.0.1:8000/reservas/' . $request->userId . '?canceled=true',
                'client_reference_id' => $request->userId,
                'metadata' => [
                    'reserva_id' => $reservaId
                ]
            ]);

            Log::info('Sesión de Stripe creada:', ['session_id' => $session->id]);
            return response()->json(['id' => $session->id]);

        } catch (\Exception $e) {
            Log::error('Error en Stripe checkout: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
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


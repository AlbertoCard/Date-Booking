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

            // Crear la sesión de checkout
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Reservación de sala',
                        ],
                        'unit_amount' => 5000, // $50.00 USD
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://localhost:5173/success',
                'cancel_url' => 'http://localhost:5173/cancel',
                'client_reference_id' => $request->userId,
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
        return response()->json(['message' => 'Pago exitoso']);
    }

    public function cancel()
    {
        return response()->json(['message' => 'Pago cancelado']);
    }
}


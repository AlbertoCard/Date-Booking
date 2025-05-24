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
            // Configurar la clave secreta de Stripe
            Stripe::setApiKey('sk_test_51RRqn4FT8eXT037JyN0N8t9toWuqkqbfw0Ufnvap54X1sBwaavvCqYU8rkbDblX1W6uNk3TBiz8o3ZSkfCDUfJQf00dhnRYxZ9');

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
            ]);

            Log::info('Sesión de Stripe creada:', ['session_id' => $session->id]);
            return response()->json(['id' => $session->id]);

        } catch (\Exception $e) {
            Log::error('Error en Stripe checkout: ' . $e->getMessage());
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


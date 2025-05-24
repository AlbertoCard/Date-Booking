<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\CiudadController;
use App\Http\Controllers\EstablecimientoController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReservaController;
use App\Models\Servicio;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\StripeController;

// Ruta de prueba
Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando correctamente',
        'status' => 'success'
    ]);
});

// Rutas de usuarios
Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::post('/', [UsuarioController::class, 'store']);
    Route::get('/obtener/{uid}', [UsuarioController::class, 'show']);
    Route::put('/{uid}/activo', [UsuarioController::class, 'updateActivo']);
    Route::put('/{uid}/activar', [UsuarioController::class, 'activarUsuario']);
    Route::put('/{uid}/estado', [UsuarioController::class, 'cambiarEstadoActivo']);
});


// Rutas de establecimientos
Route::prefix('establecimientos')->group(function () {
    Route::get('/', [EstablecimientoController::class, 'index']);
    Route::get('/estado/{opcion}', [EstablecimientoController::class, 'porEstado']);
    Route::get('/{name}', [EstablecimientoController::class, 'porNombre']);
    Route::post('/', [EstablecimientoController::class, 'store']);
    Route::put('/{id}', [EstablecimientoController::class, 'update']);
    Route::put('/estado/{id}', [EstablecimientoController::class, 'actualizarEstado']);
    Route::put('/rechazar/{id}', [EstablecimientoController::class, 'rechazarEstablecimiento']);
});

// Ruta para obtener establecimientos por usuario
Route::get('/establecimientos/usuario/{uid}', [EstablecimientoController::class, 'getByUsuario']);

// Rutas para pagos
Route::prefix('pagos')->group(function () {
    Route::get('/{id}', [PagoController::class, 'show']);

    Route::post('/', [PagoController::class, 'store']);
});


// Rutas para Reservas (públicas)
Route::prefix('reservas')->group(function () {
    Route::get('/{id}', [ReservaController::class, 'obtenerReservas']);
    Route::get('/detalle/{id}', [ReservaController::class, 'obtenerDetalleReserva']);
    Route::post('/', [ReservaController::class, 'store']);
});


// Rutas de disponibilidad
Route::post('/disponibilidad', [DisponibilidadController::class, 'store']);
Route::put('/disponibilidad/{id_servicio}/toggle', [DisponibilidadController::class, 'toggleActivo']);
Route::delete('/disponibilidad/{id_servicio}', [DisponibilidadController::class, 'destroy']);

// Rutas de servicios
Route::prefix('servicios')->group(function () {
    Route::get('/', [ServicioController::class, 'index']);
    Route::get('/{id}', [ServicioController::class, 'show']);
    Route::get('/search/{search}', [ServicioController::class, 'search']);
    Route::get('/categoria/{search}/{categoria}', [ServicioController::class, 'categoria']);
    Route::post('/', [ServicioController::class, 'store']);
    Route::get('/{id}/reseñas', [ResenaController::class, 'getByServicio']);
    Route::post('/nuevo-consultorio', [ServicioController::class, 'nuevoConsultorio']);
    Route::post('/nuevo-restaurante', [ServicioController::class, 'nuevoRestaurante']);
    Route::post('/nuevo-evento', [ServicioController::class, 'nuevoEvento']);
    Route::post('/nuevo-hotel', [ServicioController::class, 'nuevoHotel']);
});
// Rutas de ciudades
Route::get('/ciudades', [CiudadController::class, 'index']);


// Rutas para Stripe
Route::prefix('stripe')->group(function () {
    Route::post('/checkout', [StripeController::class, 'checkout']);
    Route::get('/success', [StripeController::class, 'success']);
    Route::get('/cancel', [StripeController::class, 'cancel']);
});
Route::post('/resenas', [ResenaController::class, 'store']); 

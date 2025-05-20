<?php

use App\Http\Controllers\ServicioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EstablecimientoController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\PagoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

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
    Route::put('/{uid}/estado', [UsuarioController::class, 'cambiarEstadoActivo']);
    Route::get('/obtener/{uid}', [UsuarioController::class, 'show']);
});


// Rutas de establecimientos
Route::prefix('establecimientos')->group(function () {
    Route::get('/', [EstablecimientoController::class, 'index']);
    Route::get('/estado/{opcion}', [EstablecimientoController::class, 'porEstado']);
    Route::get('/{name}', [EstablecimientoController::class, 'porNombre']);
    Route::get('/establecimientos/usuario/{uid}', [EstablecimientoController::class, 'getByUsuario']);

    Route::post('/', [EstablecimientoController::class, 'store']);

    Route::put('/{id}', [EstablecimientoController::class, 'update']);
    Route::put('/estado/{id}', [EstablecimientoController::class, 'actualizarEstado']);
    Route::put('/rechazar/{id}', [EstablecimientoController::class, 'rechazarEstablecimiento']);
});


// Rutas de disponibilidad
Route::prefix('disponibilidad')->group(function () {
    Route::get('/{id_servicio}', [ReservaController::class, 'getDisponibilidad']);
    
    Route::post('/', [DisponibilidadController::class, 'store']);

    Route::put('/{id_servicio}/toggle', [DisponibilidadController::class, 'toggleActivo']);
});


// Rutas para pagos
Route::prefix('pagos')->group(function () {
    Route::get('/{id}', [PagoController::class, 'show']);
    
    Route::post('/', [PagoController::class, 'store']);
});


// Rutas para Reservas (públicas)
Route::prefix('reservas')->group(function () {    
    Route::get('/{id}', [ReservaController::class, 'obtenerReservas']);
    
    Route::post('/', [ReservaController::class, 'store']);

});


// Rutas de servicios 
Route::prefix('servicios')->group(function () {
    Route::get('/', [ServicioController::class, 'index']);
    Route::get('/search/{search}', [ServicioController::class, 'search']);
    Route::get('/categoria/{search}/{categoria}', [ServicioController::class, 'categoria']);
    Route::get('/{id}', [ServicioController::class, 'show']);

    Route::post('/', [ServicioController::class, 'store']);
});

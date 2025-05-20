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
});

// Ruta específica para obtener usuario por UID
Route::get('usuarios/obtener/{uid}', [UsuarioController::class, 'show']);

// Rutas de establecimientos - sin grupo
Route::get('/establecimientos', [EstablecimientoController::class, 'index']);
Route::post('/establecimientos', [EstablecimientoController::class, 'store']);
Route::get('/establecimientos/usuario/{uid}', [EstablecimientoController::class, 'getByUsuario']);
Route::put('/establecimientos/{id}', [EstablecimientoController::class, 'update']); 

// Rutas de disponibilidad
Route::post('/disponibilidad', [DisponibilidadController::class, 'store']);
Route::put('/disponibilidad/{id_servicio}/toggle', [DisponibilidadController::class, 'toggleActivo']);

// Rutas para reservas y pagos
Route::post('/reservas', [ReservaController::class, 'store']);
Route::get('/disponibilidad/{id_servicio}', [ReservaController::class, 'getDisponibilidad']);
Route::post('/pagos', [PagoController::class, 'store']);
Route::get('/pagos/{id}', [PagoController::class, 'show']);

// Rutas para Reservas (públicas)
Route::get('/reservas/{id}', [ReservaController::class, 'show']);

// Rutas de servicios (públicas)
Route::get('/servicios', [ServicioController::class, 'index']);
Route::get('/servicios/search/{search}', [ServicioController::class, 'search']);
Route::get('/servicios/categoria/{search}/{categoria}', [ServicioController::class, 'categoria']);
Route::get('/servicios/{id}', [ServicioController::class, 'show']);

// Rutas de servicios (protegidas)
Route::post('/servicios', [ServicioController::class, 'store']);



Route::prefix('establecimientos')->group(function () {
    Route::get('/', [EstablecimientoController::class, 'index']);
    Route::get('/estado/{opcion}', [EstablecimientoController::class, 'porEstado']);
    Route::get('/{name}', [EstablecimientoController::class, 'porNombre']);
    Route::put('/{id}', [EstablecimientoController::class, 'actualizarEstado']);
    Route::put('/rechazar/{id}', [EstablecimientoController::class, 'rechazarEstablecimiento']);
});

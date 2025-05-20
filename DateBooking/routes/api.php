<?php

use App\Http\Controllers\ServicioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\CiudadController;
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
    Route::put('/{uid}/activo', [UsuarioController::class, 'updateActivo']);
    Route::put('/{uid}/activar', [UsuarioController::class, 'activarUsuario']);
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
});

// Rutas de ciudades
Route::get('/ciudades', [CiudadController::class, 'index']);

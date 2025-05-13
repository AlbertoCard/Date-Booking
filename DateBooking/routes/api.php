<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DisponibilidadController;
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
Route::middleware('auth:sanctum')->prefix('usuarios')->group(function () {
    Route::get('/', [UsuarioController::class, 'index']);
    Route::post('/', [UsuarioController::class, 'store']);
    Route::put('/{uid}/activo', [UsuarioController::class, 'updateActivo']);
    Route::put('/{uid}/activar', [UsuarioController::class, 'activarUsuario']);
}); 

Route::get('/servicios', [ServicioController::class, 'index']);
Route::post('/servicios', [ServicioController::class, 'store']);

// Rutas de disponibilidad
Route::post('/disponibilidad', [DisponibilidadController::class, 'store']);
Route::put('/disponibilidad/{id_servicio}/toggle', [DisponibilidadController::class, 'toggleActivo']);
// Rutas de servicios
Route::prefix('servicios')->group(function () {
    Route::get('/', [ServicioController::class, 'index']);
    Route::get('/{search}', [ServicioController::class, 'search']);
    Route::get('/categoria/{search}/{categoria}', [ServicioController::class, 'categoria']);
});


Route::get('/servicios', [ServicioController::class, 'index']);
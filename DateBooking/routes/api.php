<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\EstablecimientoController;

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

// Ruta específica para obtener usuario por UID
Route::get('usuarios/obtener/{uid}', [UsuarioController::class, 'show']);

// Rutas de establecimientos - sin grupo
Route::get('/establecimientos', [EstablecimientoController::class, 'index']);
Route::post('/establecimientos', [EstablecimientoController::class, 'store']);
Route::get('/establecimientos/usuario/{uid}', [EstablecimientoController::class, 'getByUsuario']);
Route::put('/establecimientos/{id}', [EstablecimientoController::class, 'update']); 
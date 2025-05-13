<?php

use App\Models\Servicio;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServicioController;

// Route::get('/', function () {
//     return view('vue');
// });

// Route::get('/inicio', fn() => view('vue'));
// Route::get('/nosotros', fn() => view('vue'));


//ruta que devuelve la lista de servicios
Route::get('/servicios', [ServicioController::class, 'index'])->name('servicios.index');
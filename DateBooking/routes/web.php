<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('vue');
});

Route::get('/inicio', fn() => view('vue'));
Route::get('/nosotros', fn() => view('vue'));
Route::get('/dashboard-cliente', fn() => view('vue'));
Route::get('/dashboard-establecimiento', fn() => view('vue'));

// Cualquier otra ruta no definida redirige a vue para que el router de Vue.js la maneje
Route::get('/{any}', fn() => view('vue'))->where('any', '.*');

<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('vue');
});

Route::get('/inicio', fn() => view('vue'));
Route::get('/nosotros', fn() => view('vue'));

//RUTAS RAMSES
Route::get('/nuevo-servicios', fn() => view('vue'));
route::get('/servicios-agregados', fn() => view('vue'));
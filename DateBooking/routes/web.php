<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('vue');
});

Route::get('/inicio', fn() => view('vue'));
Route::get('/nosotros', fn() => view('vue'));

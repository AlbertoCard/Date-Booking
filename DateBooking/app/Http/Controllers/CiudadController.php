<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;

class CiudadController extends Controller
{
    public function index()
    {
        $ciudades = Ciudad::select('id_ciudad', 'nombre')->orderBy('nombre')->get();
        return response()->json($ciudades);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Ciudade;

class CiudadController extends Controller
{
    public function index()
    {
        $ciudades = Ciudade::select('id_ciudad', 'nombre')->orderBy('nombre')->get();
        return response()->json($ciudades);
    }
}

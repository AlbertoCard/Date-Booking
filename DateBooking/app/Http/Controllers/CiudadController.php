<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use Illuminate\Http\Request;

class CiudadController extends Controller
{
    public function index()
    {
        $ciudades = Ciudad::select('id_ciudad', 'nombre')->orderBy('nombre')->get();
        return response()->json($ciudades);
    }
} 
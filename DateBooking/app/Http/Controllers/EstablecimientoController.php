<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use Illuminate\Http\Request;

class EstablecimientoController extends Controller
{
    //

    function index()
    {
        $establecimientos = Establecimiento::all();
        return response()->json($establecimientos);
    }

    function show($id)
    {
        $establecimiento = Establecimiento::where('id_establecimiento', $id)->get();
        if ($establecimiento) {
            return response()->json($establecimiento);
        } else {
            return response()->json(['message' => 'Establecimiento no encontrado'], 404);
        }
    }

    function porEstado($opcion)
    {
        $establecimientos = Establecimiento::where('estado', $opcion)->get();
        return response()->json($establecimientos);
    }
}

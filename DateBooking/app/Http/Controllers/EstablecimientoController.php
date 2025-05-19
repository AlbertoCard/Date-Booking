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

    function porNombre($name)
    {
        $establecimiento = Establecimiento::where('nombre', $name)->first();
        if (!$establecimiento) {
            return response()->json(['message' => 'No se encontró un establecimiento con ese nombre'], 404);
        }
        return response()->json($establecimiento);
    }

    function porEstado($opcion)
    {
        $establecimientos = Establecimiento::where('estado', $opcion)->get();
        return response()->json($establecimientos);
    }
    function actualizarEstado($id)
    {
        $establecimiento = Establecimiento::where('id_establecimiento', $id)->first();
        if (!$establecimiento) {
            return response()->json(['message' => 'Establecimiento no encontrado'], 404);
        }

        // Cambia el estado actual: si es 'activo' pasa a 'inactivo', si es 'inactivo' pasa a 'activo'
        $establecimiento->estado = ($establecimiento->estado === 'activo') ? 'inactivo' : 'activo';
        $establecimiento->save();

        return response()->json($establecimiento);
    }

    function rechazarEstablecimiento($id)
    {
        $establecimiento = Establecimiento::where('id_establecimiento', $id)->first();
        if (!$establecimiento) {
            return response()->json(['message' => 'Establecimiento no encontrado'], 404);
        }

        $establecimiento->estado = 'rechazado';
        $establecimiento->save();

        return response()->json($establecimiento);
    }
}

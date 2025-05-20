<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    // lista de servicios
    public function index()
    {
        $servicios = Servicio::with('disponibilidad')->get();
        return response()->json($servicios);
    }

    public function show($id)
    {
        $servicio = Servicio::with('disponibilidad')->find($id);
        
        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json($servicio);
    }

    // crear nuevo servicio
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'costo' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:255',
            'id_ciudad' => 'required|exists:ciudades,id_ciudad'
        ]);

        $servicio = Servicio::create([
            'id_establecimiento' => 1, // Por ahora fijo
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'costo' => $request->costo,
            'categoria' => $request->categoria,
            'id_ciudad' => $request->id_ciudad
        ]);

        return response()->json($servicio, 201);
    }
  
    public function search($search)
    {
        return Servicio::where('nombre', 'like', '%' . $search . '%')
            ->get();
    }

    public function categoria($search, $categoria)
    {
        return Servicio::where('nombre', 'like', '%' . $search . '%')
            ->where('categoria', 'like', '%' . $categoria . '%')
            ->get();
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    // lista de servicios
    public function index()
    {
        try {
            $servicios = Servicio::select(
                'id_servicio',
                'nombre',
                'descripcion',
                'costo',
                'categoria',
                'id_ciudad'
            )->get();

            return response()->json($servicios);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los servicios',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    // crear nuevo servicio
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'costo' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:255'
        ]);

        $servicio = Servicio::create([
            'id_establecimiento' => 1, // Por ahora fijo
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'costo' => $request->costo,
            'categoria' => $request->categoria,
            'id_ciudad' => 1
        ]);

        return response()->json($servicio, 201);
    }
  
    public function search($search)
    {
        return Servicio::where('nombre', 'like', '%' . $search . '%')
            ->get();
    }

    public function show($id)
    {
        try {
            \Log::info('Buscando servicio con ID: ' . $id);
            
            $servicio = Servicio::select(
                'id_servicio',
                'id_establecimiento',
                'nombre',
                'descripcion',
                'costo',
                'categoria',
                'id_ciudad'
            )
            ->where('id_servicio', $id)
            ->first();
            
            if (!$servicio) {
                \Log::warning('Servicio no encontrado con ID: ' . $id);
                return response()->json([
                    'message' => 'Servicio no encontrado',
                    'id_buscado' => $id
                ], 404);
            }
            
            \Log::info('Servicio encontrado:', $servicio->toArray());
            return response()->json($servicio);
            
        } catch (\Exception $e) {
            \Log::error('Error al buscar servicio: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al obtener el servicio',
                'error' => $e->getMessage(),
                'id_buscado' => $id
            ], 500);
        }
    }

    public function categoria($search, $categoria)
    {
        return Servicio::where('nombre', 'like', '%' . $search . '%')
            ->where('categoria', 'like', '%' . $categoria . '%')
            ->get();
    }
}


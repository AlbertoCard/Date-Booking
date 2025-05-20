<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServicioController extends Controller
{
    // lista de servicios
    public function index()
    {
        $servicios = Servicio::with(['disponibilidad', 'imagen'])->get();
        \Log::info('Servicios cargados:', ['servicios' => $servicios->toArray()]);
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
            'id_ciudad' => 'required|exists:ciudades,id_ciudad',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $servicio = Servicio::create([
            'id_establecimiento' => 1, // Por ahora fijo
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'costo' => $request->costo,
            'categoria' => $request->categoria,
            'id_ciudad' => $request->id_ciudad
        ]);

        // Guardar la imagen si se proporciona
        if ($request->hasFile('imagen')) {
            try {
                $imagen = $request->file('imagen');
                // Limpiar el nombre del archivo
                $extension = $imagen->getClientOriginalExtension();
                $nombreLimpio = preg_replace('/[^a-zA-Z0-9]/', '_', pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME));
                $nombreImagen = time() . '_' . $nombreLimpio . '.' . $extension;
                
                // Guardar en public/images
                $imagen->move(public_path('images'), $nombreImagen);
                
                // Crear registro en la tabla imagenes
                $imagenModel = Imagen::create([
                    'id_servicio' => $servicio->id_servicio,
                    'url' => '/images/' . $nombreImagen
                ]);
                
                \Log::info('Imagen guardada:', [
                    'nombre_original' => $imagen->getClientOriginalName(),
                    'nombre_limpio' => $nombreImagen,
                    'url' => '/images/' . $nombreImagen
                ]);
            } catch (\Exception $e) {
                \Log::error('Error al guardar la imagen: ' . $e->getMessage());
            }
        }

        return response()->json($servicio->load('imagen'), 201);
    }
  
    public function search($search)
    {
        return Servicio::where('nombre', 'like', '%' . $search . '%')
            ->with('imagen')
            ->get();
    }

    public function categoria($search, $categoria)
    {
        return Servicio::where('nombre', 'like', '%' . $search . '%')
            ->where('categoria', 'like', '%' . $categoria . '%')
            ->with('imagen')
            ->get();
    }
}


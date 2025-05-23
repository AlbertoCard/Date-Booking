<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\MedicoController;
use App\Models\Disponibilidad;

class ServicioController extends Controller
{
    protected $disponibilidadController;
    protected $medicoController;


    // lista de servicios
    public function index()
    {
        $servicios = Servicio::with(['disponibilidad', 'imagen'])->get();
        Log::info('Servicios cargados:', ['servicios' => $servicios->toArray()]);
        return response()->json($servicios);
    }

    public function show($id)
    {
        $servicio = Servicio::with(['disponibilidad', 'ciudad', 'imagen'])->find($id);

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

                Log::info('Imagen guardada:', [
                    'nombre_original' => $imagen->getClientOriginalName(),
                    'nombre_limpio' => $nombreImagen,
                    'url' => '/images/' . $nombreImagen
                ]);
            } catch (\Exception $e) {
                Log::error('Error al guardar la imagen: ' . $e->getMessage());
            }
        }

        return response()->json($servicio->load('imagen'), 201);
    }
    // buscar servicio
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


    //nuevo consultorio
    // Esta función crea un nuevo consultorio con disponibilidad y médicos asociados.
    // Posibles fallos:
    // - Nombre de servicio ya existe para el establecimiento.
    // - Error en la validación de los datos de entrada.
    // - Error al crear el servicio, disponibilidad o médicos (por ejemplo, por problemas en la base de datos).
    // - Faltan campos requeridos en el request.
    // - El método no tiene el modificador de visibilidad (debe ser public).

    public function nuevoConsultorio(Request $request)
    {
        $request->validate([
            'id_establecimiento' => 'required|integer',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'costo' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:255',
            'id_ciudad' => 'required|exists:ciudades,id_ciudad',
            'disponibilidad' => 'required|array|min:1',
            'disponibilidad.*.hora_inicio' => 'required|string',
            'disponibilidad.*.hora_fin' => 'required|string',
            'disponibilidad.*.intervalo' => 'required|date_format:H:i:s',
            'disponibilidad.*.dias' => 'required|string',
            'disponibilidad.*.tipo' => 'required|string',
            'disponibilidad.*.activo' => 'required|integer',
            'medicos' => 'required|array|min:1',
            'medicos.*.nombre' => 'required|string|max:255',
            'medicos.*.especialidad' => 'required|string|max:255'
        ]);

        DB::beginTransaction();

        try {
            // Verificar si ya existe un servicio con el mismo nombre para el establecimiento
            $existe = Servicio::where('id_establecimiento', $request->id_establecimiento)
                ->where('nombre', $request->nombre)
                ->exists();

            if ($existe) {
                DB::rollBack();
                return response()->json(['error' => 'Ya existe un servicio con ese nombre para este establecimiento.'], 409);
            }


            $servicio = Servicio::create([
                'id_establecimiento' => $request->id_establecimiento,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'costo' => $request->costo,
                'categoria' => $request->categoria,
                'id_ciudad' => $request->id_ciudad
            ]);

            $id_servicio = $servicio->id_servicio;

            // Guardar disponibilidad
            foreach ($request->disponibilidad as $disp) {
                \App\Models\Disponibilidad::create([
                    'id_servicio' => $id_servicio,
                    'hora_inicio' => $disp['hora_inicio'],
                    'hora_fin' => $disp['hora_fin'],
                    'intervalo' => $disp['intervalo'],
                    'dias' => $disp['dias'],
                    'tipo' => $disp['tipo'],
                    'activo' => $disp['activo'],
                ]);
            }

            // Guardar médicos
            foreach ($request->medicos as $medico) {
                \App\Models\Medico::create([
                    'id_servicio' => $id_servicio,
                    'nombre' => $medico['nombre'],
                    'especialidad' => $medico['especialidad']
                ]);
            }

            DB::commit();

            return response()->json($servicio->load(['disponibilidad', 'imagen']), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el consultorio: ' . $e->getMessage()], 500);
        }
    }

    //nuevo restaurante
    // Esta función crea un nuevo restaurante con disponibilidad y mesas asociadas.
    // Posibles fallos:
    // - Nombre de servicio ya existe para el establecimiento.
    // - Error en la validación de los datos de entrada.
    // - Error al crear el servicio, disponibilidad o mesas (por ejemplo, por problemas en la base de datos).
    // - Faltan campos requeridos en el request.
    // - El método no tiene el modificador de visibilidad (debe ser public).

    public function nuevoRestaurante(Request $request)
    {
        $request->validate([
            'id_establecimiento' => 'required|integer',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'costo' => 'required|numeric|min:0',
            'categoria' => 'required|string|max:255',
            'id_ciudad' => 'required|exists:ciudades,id_ciudad',
            'disponibilidad' => 'required|array|min:1',
            'disponibilidad.*.hora_inicio' => 'required|string',
            'disponibilidad.*.hora_fin' => 'required|string',
            'disponibilidad.*.intervalo' => 'required|date_format:H:i:s',
            'disponibilidad.*.dias' => 'required|string',
            'disponibilidad.*.tipo' => 'required|string',
            'disponibilidad.*.activo' => 'required|integer',
            'mesas' => 'required|array|min:1',
            'mesas.*.capacidad' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();

        try {
            // Verificar si ya existe un servicio con el mismo nombre para el establecimiento
            $existe = Servicio::where('id_establecimiento', $request->id_establecimiento)
                ->where('nombre', $request->nombre)
                ->exists();

            if ($existe) {
                DB::rollBack();
                return response()->json(['error' => 'Ya existe un servicio con ese nombre para este establecimiento.'], 409);
            }
            $servicio = Servicio::create([
                'id_establecimiento' => $request->id_establecimiento,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'costo' => $request->costo,
                'categoria' => $request->categoria,
                'id_ciudad' => $request->id_ciudad
            ]);
            $id_servicio = $servicio->id_servicio;
            // Guardar disponibilidad
            foreach ($request->disponibilidad as $disp) {
                \App\Models\Disponibilidad::create([
                    'id_servicio' => $id_servicio,
                    'hora_inicio' => $disp['hora_inicio'],
                    'hora_fin' => $disp['hora_fin'],
                    'intervalo' => $disp['intervalo'],
                    'dias' => $disp['dias'],
                    'tipo' => $disp['tipo'],
                    'activo' => $disp['activo'],
                ]);
            }
            // Guardar mesas
            foreach ($request->mesas as $mesa) {
                \App\Models\Mesa::create([
                    'id_servicio' => $id_servicio,
                    'capacidad' => $mesa['capacidad']
                ]);
            }
            DB::commit();
            return response()->json($servicio->load(['disponibilidad', 'imagen']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el restaurante: ' . $e->getMessage()], 500);
        } 
    }
}

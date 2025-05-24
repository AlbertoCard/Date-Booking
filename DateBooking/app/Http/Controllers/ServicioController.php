<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use App\Models\Imagen;
use App\Models\Reseña;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DisponibilidadController;
use App\Http\Controllers\MedicoController;
use App\Models\Disponibilidad;
use \Illuminate\Validation\ValidationException;

class ServicioController extends Controller
{
    protected $disponibilidadController;
    protected $medicoController;


    public function index(Request $request)
    {
        $idEstablecimiento = $request->query('id_establecimiento');
        
        $query = Servicio::with(['disponibilidad', 'imagen']);
        
        if ($idEstablecimiento) {
            $query->where('id_establecimiento', $idEstablecimiento);
        }
        
        $servicios = $query->get();
        Log::info('Servicios cargados:', ['servicios' => $servicios->toArray()]);
        return response()->json($servicios);
    }
    
    public function show($id)
    {
        $servicio = Servicio::with(['disponibilidad', 'ciudad', 'imagen', 'establecimiento'])->find($id);

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        return response()->json($servicio);
    }

    public function getResenas($id)
    {
        $servicio = Servicio::find($id);

        if (!$servicio) {
            return response()->json(['message' => 'Servicio no encontrado'], 404);
        }

        $resenas = Reseña::with('usuario')
            ->where('id_servicio', $id)
            ->orderBy('fecha', 'desc')
            ->get();

        $promedio = $resenas->avg('calificacion') ?? 0;
        $total = $resenas->count();

        return response()->json([
            'resenas' => $resenas,
            'promedio' => round($promedio, 1),
            'total' => $total
        ]);
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
            'id_establecimiento' => 'required|exists:establecimientos,id_establecimiento',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $servicio = Servicio::create([
            'id_establecimiento' => $request->id_establecimiento,
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
        try {
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
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error en la validación de los datos: ' . $e->getMessage()], 422);
        }

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
        try {
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
                'mesas.*.personas' => 'required|integer|min:1'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error en la validación de los datos: ' . $e->getMessage()], 422);
        }

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
                    'personas' => $mesa['personas']
                ]);
            }
            DB::commit();
            return response()->json($servicio->load(['disponibilidad', 'imagen']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el restaurante: ' . $e->getMessage()], 500);
        }
    }

    //nuevo evento
    // Esta función crea un nuevo evento con disponibilidad y médicos asociados.
    // Posibles fallos:
    // - Nombre de servicio ya existe para el establecimiento.
    // - Error en la validación de los datos de entrada.
    // - Error al crear el servicio, disponibilidad o médicos (por ejemplo, por problemas en la base de datos).
    // - Faltan campos requeridos en el request.
    // - El método no tiene el modificador de visibilidad (debe ser public).

    public function nuevoEvento(Request $request)
    {
        try {
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
                'disponibilidad.*.tipo' => 'required|string',
                'disponibilidad.*.activo' => 'required|integer',
                'lugares' => 'required|array|min:1',
                'lugares.*.filas' => 'required|integer|min:1',
                'lugares.*.numeros' => 'required|integer|min:1'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error en la validación de los datos: ' . $e->getMessage()], 422);
        }

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
                    'dias' => 'Lunes',
                    'tipo' => $disp['tipo'],
                    'activo' => $disp['activo'],
                ]);
            }

            // Definir sectores y letras de filas
            $sectores = ['norte', 'sur', 'este', 'oeste'];
            $letrasFilas = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

            try {
                foreach ($request->lugares as $lugarData) {
                    $filasCount = $lugarData['filas'];
                    $numerosCount = $lugarData['numeros'];

                    for ($sectorIndex = 0; $sectorIndex < count($sectores); $sectorIndex++) {
                        $sector = $sectores[$sectorIndex];
                        for ($fila = 0; $fila < $filasCount && $fila < count($letrasFilas); $fila++) {
                            $letraFila = $letrasFilas[$fila];
                            for ($numero = 1; $numero <= $numerosCount; $numero++) {
                                \App\Models\Lugare::create([
                                    'id_servicio' => $id_servicio,
                                    'fila' => $letraFila,
                                    'numero' => $numero,
                                    'sector' => $sector
                                ]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error al crear los lugares: ' . $e->getMessage()], 500);
            }

            DB::commit();

            // Cargar relaciones si existen en el modelo
            return response()->json($servicio->load(['disponibilidad', 'imagen']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el evento: ' . $e->getMessage()], 500);
        }
    }


    // nuevo hotel
    // Esta función crea un nuevo hotel con disponibilidad y la habitacion asociada.
    // Posibles fallos:
    // - Nombre de servicio ya existe para el establecimiento.
    // - Error en la validación de los datos de entrada.
    // - Error al crear el servicio, disponibilidad o habitaciones (por ejemplo, por problemas en la base de datos).
    // - Faltan campos requeridos en el request.
    // - El método no tiene el modificador de visibilidad (debe ser public).    
    public function nuevoHotel(Request $request)
    {
        try {
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
                'disponibilidad.*.tipo' => 'required|string',
                'disponibilidad.*.activo' => 'required|integer',
                'habitacion' => 'required|array|min:1',
                'habitacion.*.tipo' => 'required|string|max:255',
                'habitacion.*.numero' => 'required|integer|min:1',
                'habitacion.*.capacidad' => 'required|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error en la validación de los datos: ' . $e->getMessage()], 422);
        }

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
                    'dias' => 'Lunes',
                    'tipo' => $disp['tipo'],
                    'activo' => $disp['activo'],
                ]);
            }

            // Guardar habitaciones
            try {
                foreach ($request->habitacion as $hab) {
                    \App\Models\Habitacione::create([
                        'id_servicio' => $id_servicio,
                        'tipo' => $hab['tipo'],
                        'numero' => $hab['numero'],
                        'capacidad' => $hab['capacidad'],
                        'fecha_inicio' => now(), 
                        'fecha_fin' => now()->addDays(5)
                    ]);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Error al crear las habitaciones: ' . $e->getMessage()], 500);
            }

            DB::commit();

            return response()->json($servicio->load(['disponibilidad', 'imagen']), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el hotel: ' . $e->getMessage()], 500);
        }
    }
}

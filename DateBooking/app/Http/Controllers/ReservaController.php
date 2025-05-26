<?php

namespace App\Http\Controllers;

use App\Models\Reserva;

use App\Models\Disponibilidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required|string',
                'id_servicio' => 'required|integer|exists:servicios,id_servicio',
                'id_pago' => 'required|integer|exists:pagos,id_pago',
                'estado' => 'required|string',
                'fecha' => 'required|date',
                'tipo_servicio' => 'required|string',
                'detalle_1' => 'required|string',
                'detalle_2' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verificar disponibilidad
            $fecha = Carbon::parse($request->fecha);
            $disponibilidad = Disponibilidad::where('id_servicio', $request->id_servicio)
                ->where(function ($query) use ($fecha) {
                    $query->where('fecha', $fecha->format('Y-m-d'))
                        ->orWhere('dias', strtolower($fecha->format('l')));
                })
                ->where('activo', 1)
                ->first();

            if (!$disponibilidad) {
                return response()->json([
                    'message' => 'No hay disponibilidad para la fecha seleccionada'
                ], 400);
            }

            // Verificar si ya existe una reserva para esa fecha y hora
            $reservaExistente = Reserva::where('id_servicio', $request->id_servicio)
                ->where('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->first();

            if ($reservaExistente) {
                return response()->json([
                    'message' => 'Ya existe una reserva para esta fecha y hora'
                ], 400);
            }

            $reserva = Reserva::create($request->all());

            DB::commit();

            return response()->json([
                'message' => 'Reserva creada exitosamente',
                'id_reserva' => $reserva->id_reserva
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $reserva = Reserva::with(['usuario', 'servicio', 'pago'])->findOrFail($id);
            return response()->json($reserva);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Reserva no encontrada'], 404);
        }
    }

    public function getDisponibilidad($id_servicio, Request $request)
    {
        try {
            $fecha = Carbon::parse($request->fecha);

            $disponibilidad = Disponibilidad::where('id_servicio', $id_servicio)
                ->where(function ($query) use ($fecha) {
                    $query->where('fecha', $fecha)
                        ->orWhere('dias', strtolower($fecha->format('l')));
                })
                ->where('activo', 1)
                ->first();

            if (!$disponibilidad) {
                return response()->json([], 200);
            }

            return response()->json($disponibilidad);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener disponibilidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function obtenerReservas($id)
    {
        try {
            $reservas = Reserva::with(['servicio:id_servicio,nombre'])
                ->where('id_usuario', $id)
                ->where('estado', '!=', 'apartado')
                ->get();

            return response()->json(['reservas' => $reservas], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener reservas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function obtenerDetalleReserva($id)
    {
        try {
            $reserva = Reserva::with(['servicio.establecimiento', 'pago'])
                ->findOrFail($id);

            return response()->json([
                'reserva' => $reserva
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener el detalle de la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reservarConsultorio(Request $request)
    {
        // validar datos de entrada
        try {
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required|string',
                'id_servicio' => 'required|integer|exists:servicios,id_servicio',
                'estado' => 'required|string',
                'fecha' => 'required|date_format:Y-m-d H:i:s',
                'tipo_servicio' => 'required|string|in:consultorio',
                'detalle_1' => 'required|integer'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la validación de los datos: ' . $e->getMessage()], 422);
        }

        $disponibilidad = Disponibilidad::where('id_servicio', $request->id_servicio)
            ->where('activo', 1)
            ->first();

        if (!$disponibilidad) {
            return response()->json([
                'message' => 'El servicio no está disponible actualmente'
            ], 400);
        }

        // Validar que la hora de la reserva esté dentro del rango permitido
        $horaReserva = Carbon::parse($request->fecha)->format('H:i:s');
        $horaInicio = Carbon::parse($disponibilidad->hora_inicio)->format('H:i:s');
        $horaFin = Carbon::parse($disponibilidad->hora_fin)->format('H:i:s');

        if ($horaReserva < $horaInicio || $horaReserva > $horaFin) {
            return response()->json([
                'message' => 'La hora seleccionada está fuera del horario disponible'
            ], 400);
        }


        DB::beginTransaction();

        try {
            // validar si la reserva ya existe
            // Usar SELECT FOR UPDATE para bloquear el registro si existe
            $reservaExistente = Reserva::where('id_servicio', $request->id_servicio)
                ->where('fecha', $request->fecha)
                ->where('tipo_servicio', 'consultorio')
                ->where('detalle_1', (string)$request->detalle_1)
                ->lockForUpdate()
                ->first();

            $ahora = Carbon::now();

            if (!$reservaExistente) {
                // crear la reserva
                $reserva = new Reserva();
                $reserva->id_usuario = $request->id_usuario;
                $reserva->id_servicio = $request->id_servicio;
                $reserva->estado = "apartado";
                $reserva->fecha = $request->fecha;
                $reserva->tipo_servicio = "consultorio";
                $reserva->detalle_1 = (string)$request->detalle_1;
                $reserva->detalle_2 = "";
                $reserva->disponible_hasta = $ahora->copy()->addMinutes(15);
                $reserva->save();

                DB::commit();
                return response()->json([
                    'message' => 'Reserva creada exitosamente',
                    'id_reserva' => $reserva->id_reserva
                ], 201);
            }


            if (
                $reservaExistente->estado === 'apartado' &&
                $reservaExistente->disponible_hasta &&
                Carbon::parse($reservaExistente->disponible_hasta)->lt($ahora)
            ) {
                // Actualizar id_usuario y disponible_hasta
                $reservaExistente->id_usuario = $request->id_usuario;
                $reservaExistente->disponible_hasta = $ahora->copy()->addMinutes(15);
                $reservaExistente->save();

                DB::commit();
                return response()->json([
                    'message' => 'Reserva actualizada exitosamente',
                    'id_reserva' => $reservaExistente->id_reserva
                ], 200);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'Ya existe una reserva para este consultorio en la fecha y hora seleccionada'
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al hacer la reserva: ' . $e->getMessage()], 422);
        }
    }



    public function reservarRestaurante(Request $request)
    {
        // Validar datos de entrada
        try {
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required|string',
                'id_servicio' => 'required|integer|exists:servicios,id_servicio',
                'estado' => 'required|string',
                'fecha' => 'required|date_format:Y-m-d H:i:s',
                'tipo_servicio' => 'required|string|in:restaurante',
                'personas' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la validación de los datos: ' . $e->getMessage()], 422);
        }

        //Verificar disponibilidad del servicio
        $disponibilidad = Disponibilidad::where('id_servicio', $request->id_servicio)
            ->where('activo', 1)
            ->first();

        if (!$disponibilidad) {
            return response()->json([
                'message' => 'El servicio no está disponible actualmente'
            ], 400);
        }

        // Validar que la hora de la reserva esté dentro del rango permitido
        $horaReserva = Carbon::parse($request->fecha)->format('H:i:s');
        $horaInicio = Carbon::parse($disponibilidad->hora_inicio)->format('H:i:s');
        $horaFin = Carbon::parse($disponibilidad->hora_fin)->format('H:i:s');

        if ($horaReserva < $horaInicio || $horaReserva > $horaFin) {
            return response()->json([
                'message' => 'La hora seleccionada está fuera del horario disponible'
            ], 400);
        }


        // Buscar mesas asociadas al servicio que tengan capacidad suficiente
        $mesasDisponibles = DB::table('mesas')
            ->where('id_servicio', $request->id_servicio)
            ->where('personas', '=', $request->personas)
            ->pluck('id_mesa');

        if ($mesasDisponibles->isEmpty()) {
            return response()->json([
                'message' => 'No hay mesas disponibles para la cantidad de personas solicitada'
            ], 400);
        }

        DB::beginTransaction();

        try {
            $mesaAsignada = null;
            $ahora = Carbon::now();

            foreach ($mesasDisponibles as $id_mesa) {
                // Buscar reserva existente para esa mesa, fecha y hora
                $reservaExistente = Reserva::where('id_servicio', $request->id_servicio)
                    ->where('fecha', $request->fecha)
                    ->where('tipo_servicio', 'restaurante')
                    ->where('detalle_1', (string)$id_mesa)
                    ->lockForUpdate()
                    ->first();

                if (!$reservaExistente) {
                    $mesaAsignada = $id_mesa;
                    break;
                }

                // Si la reserva está "apartado" y expiró, reasignar
                if (
                    $reservaExistente->estado === 'apartado' &&
                    $reservaExistente->disponible_hasta &&
                    Carbon::parse($reservaExistente->disponible_hasta)->lt($ahora)
                ) {
                    // Actualizar id_usuario y disponible_hasta
                    $reservaExistente->id_usuario = $request->id_usuario;
                    $reservaExistente->disponible_hasta = $ahora->copy()->addMinutes(15);
                    $reservaExistente->save();

                    DB::commit();
                    return response()->json([
                        'message' => 'Reserva actualizada exitosamente',
                        'id_reserva' => $reservaExistente->id_reserva,
                        'id_mesa' => $id_mesa
                    ], 200);
                }
            }

            if (!$mesaAsignada) {
                DB::rollBack();
                return response()->json([
                    'message' => 'No hay mesas disponibles para la fecha y hora seleccionadas'
                ], 400);
            }

            // Crear la reserva
            $reserva = new Reserva();
            $reserva->id_usuario = $request->id_usuario;
            $reserva->id_servicio = $request->id_servicio;
            $reserva->estado = "apartado";
            $reserva->fecha = $request->fecha;
            $reserva->tipo_servicio = "restaurante";
            $reserva->detalle_1 = (string)$mesaAsignada;
            $reserva->detalle_2 = $request->personas;
            $reserva->disponible_hasta = $ahora->copy()->addMinutes(15);
            $reserva->save();

            DB::commit();
            return response()->json([
                'message' => 'Reserva creada exitosamente',
                'id_reserva' => $reserva->id_reserva,
                'id_mesa' => $mesaAsignada
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al hacer la reserva: ' . $e->getMessage()], 422);
        }
    }

    //reservar evento
    public function reservarEvento(Request $request)
    {
        try {
            Log::info('Datos recibidos en reservarEvento:', $request->all());

            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required|string',
                'id_servicio' => 'required|integer|exists:servicios,id_servicio',
                'estado' => 'required|string',
                'fecha' => 'required|date_format:Y-m-d H:i:s',
                'tipo_servicio' => 'required|string|in:evento',
                'lugares' => 'required|array|min:1',
                'lugares.*' => ['required', 'string', function ($attribute, $value, $fail) {
                    if (!preg_match('/^[a-zA-Z]+[a-zA-Z]\d+$/', $value)) {
                        Log::error('Formato de lugar inválido:', ['lugar' => $value]);
                        $fail('El formato del lugar ' . $value . ' es inválido. Debe ser sector + fila + número (ej: oestea4)');
                    }
                }]
            ]);

            if ($validator->fails()) {
                Log::error('Error de validación:', $validator->errors()->toArray());
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }
        } catch (\Exception $e) {
            Log::error('Error en la validación:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error en la validación de los datos: ' . $e->getMessage()], 422);
        }

        DB::beginTransaction();

        try {
            $ahora = Carbon::now();
            $lugaresSolicitados = $request->lugares;
            Log::info('Lugares solicitados:', $lugaresSolicitados);
            $lugaresOcupados = [];

            // Eliminar reservas "apartado" expiradas antes de continuar
            Reserva::where('id_servicio', $request->id_servicio)
                ->where('fecha', $request->fecha)
                ->where('tipo_servicio', 'evento')
                ->where('estado', 'apartado')
                ->whereNotNull('disponible_hasta')
                ->where('disponible_hasta', '<', $ahora)
                ->delete();

            // Buscar reservas existentes para esa fecha y hora
            $reservasExistentes = Reserva::where('id_servicio', $request->id_servicio)
                ->where('fecha', $request->fecha)
                ->where('tipo_servicio', 'evento')
                ->lockForUpdate()
                ->get();

            Log::info('Reservas existentes encontradas:', ['count' => $reservasExistentes->count()]);

            // Revisar si alguno de los lugares ya está reservado
            foreach ($reservasExistentes as $reservaExistente) {
                $lugaresReservados = json_decode($reservaExistente->detalle_1, true);
                Log::info('Lugares reservados en reserva existente:', ['lugares' => $lugaresReservados]);

                if (!is_array($lugaresReservados)) {
                    $lugaresReservados = [];
                    $asientos = array_map('trim', explode(',', $reservaExistente->detalle_1));
                    foreach ($asientos as $asiento) {
                        $lugaresReservados[] = $asiento;
                    }
                }
                foreach ($lugaresSolicitados as $lugarSolicitado) {
                    if (in_array($lugarSolicitado, $lugaresReservados)) {
                        $lugaresOcupados[] = $lugarSolicitado;
                    }
                }
            }

            if (!empty($lugaresOcupados)) {
                Log::info('Lugares ocupados encontrados:', $lugaresOcupados);
                DB::rollBack();
                return response()->json([
                    'message' => 'Algunos lugares ya están reservados para la fecha y hora seleccionadas',
                    'lugares_ocupados' => $lugaresOcupados
                ], 400);
            }

            // Guardar los lugares como JSON en detalle_1
            $reserva = new Reserva();
            $reserva->id_usuario = $request->id_usuario;
            $reserva->id_servicio = $request->id_servicio;
            $reserva->estado = "apartado";
            $reserva->fecha = $request->fecha;
            $reserva->tipo_servicio = "evento";
            $reserva->detalle_1 = json_encode($lugaresSolicitados);
            $reserva->detalle_2 = "";
            $reserva->disponible_hasta = $ahora->copy()->addMinutes(15);
            $reserva->save();

            Log::info('Reserva creada exitosamente:', [
                'id_reserva' => $reserva->id_reserva,
                'lugares' => $lugaresSolicitados
            ]);

            DB::commit();
            return response()->json([
                'message' => 'Reserva creada exitosamente',
                'id_reserva' => $reserva->id_reserva,
                'lugares' => $lugaresSolicitados
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear la reserva:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            DB::rollBack();
            return response()->json(['error' => 'Error al hacer la reserva: ' . $e->getMessage()], 422);
        }
    }

    public function reservarHotel(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_usuario' => 'required|string',
                'id_servicio' => 'required|integer|exists:servicios,id_servicio',
                'estado' => 'required|string',
                'tipo_servicio' => 'required|string|in:hotel',
                'fecha_inicio' => 'required|date|after_or_equal:today',
                'fecha_fin' => 'required|date|after:fecha_inicio'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la validación de los datos: ' . $e->getMessage()], 422);
        }

        // Buscar habitaciones asociadas al servicio
        $habitacionesDisponibles = DB::table('habitaciones')
            ->where('id_servicio', $request->id_servicio)
            ->pluck('id_habitacion');

        if ($habitacionesDisponibles->isEmpty()) {
            return response()->json([
                'message' => 'No hay habitaciones disponibles para este servicio'
            ], 400);
        }

        $fechaInicio = Carbon::parse($request->fecha_inicio)->format('Y-m-d');
        $fechaFin = Carbon::parse($request->fecha_fin)->format('Y-m-d');

        DB::beginTransaction();

        try {
            $habitacionAsignada = null;

            foreach ($habitacionesDisponibles as $id_habitacion) {
                // Buscar reservas existentes que se crucen con el rango solicitado
                $reservaExistente = Reserva::where('id_servicio', $request->id_servicio)
                    ->where('tipo_servicio', 'hotel')
                    ->where('detalle_1', (string)$id_habitacion)
                    ->where(function ($query) use ($fechaInicio, $fechaFin) {
                        $query->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(detalle_2, ',', 1), '%Y-%m-%d') <= ?", [$fechaFin])
                            ->whereRaw("STR_TO_DATE(SUBSTRING_INDEX(detalle_2, ',', -1), '%Y-%m-%d') >= ?", [$fechaInicio]);
                    })
                    ->where('estado', '!=', 'cancelada')
                    ->lockForUpdate()
                    ->first();

                if (!$reservaExistente) {
                    $habitacionAsignada = $id_habitacion;
                    break;
                }
            }

            if (!$habitacionAsignada) {
                DB::rollBack();
                return response()->json([
                    'message' => 'No hay habitaciones disponibles para las fechas seleccionadas'
                ], 400);
            }

            $ahora = Carbon::now();

            $reserva = new Reserva();
            $reserva->id_usuario = $request->id_usuario;
            $reserva->id_servicio = $request->id_servicio;
            $reserva->estado = "apartada";
            $reserva->fecha = $fechaInicio;
            $reserva->tipo_servicio = "hotel";
            $reserva->detalle_1 = (string)$habitacionAsignada;
            $reserva->detalle_2 = $fechaInicio . ',' . $fechaFin;
            $reserva->disponible_hasta = $ahora->copy()->addMinutes(15);
            $reserva->save();

            DB::commit();
            return response()->json([
                'message' => 'Reserva creada exitosamente',
                'id_reserva' => $reserva->id_reserva,
                'id_habitacion' => $habitacionAsignada
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al hacer la reserva: ' . $e->getMessage()], 422);
        }
    }

    public function getReservasByServicio($id_servicio)
    {

        // Obtenemos las reservas de un servicio
        $reservas = Reserva::where('id_servicio', $id_servicio)
            ->get();

        return response()->json(['reservas' => $reservas], 200);
    }

    public function validarReserva(Request $request)
    {

        Log::info('id_reserva recibido: ' . $request->input('id_reserva'));
        Log::info('estado recibido: ' . $request->input('estado'));

        $validatedData = $request->validate([
            'id_reserva' => 'required|integer',
            'estado' => 'required|string',
        ]);

        $id_reserva = $validatedData['id_reserva'];
        $estado = $validatedData['estado'];

        $reserva = Reserva::find($id_reserva);

        if (!$reserva) {
            return response()->json(['error' => 'No se encontró la reserva.'], 404);
        }

        if ($reserva->fecha < now()) {
            return response()->json(['error' => 'La reserva ya expiró.'], 400);
        }

        if ($reserva->estado !== 'pendiente') {
            return response()->json(['message' => 'La reserva no puede ser validada.'], 400);
        }

        if ($reserva->estado === $estado) {
            return response()->json(['message' => 'La reserva ya tiene este estado.'], 200);
        }

        DB::beginTransaction();

        try {
            $reserva->estado = $estado;
            $reserva->save();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Reserva actualizada.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar reserva: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al actualizar la reserva.'], 500);
        }
    }

    public function getLugaresOcupadosEvento($id_servicio)
    {
        try {
            Log::info('Obteniendo lugares ocupados para el servicio:', ['id_servicio' => $id_servicio]);

            // Obtener todas las reservas activas para el servicio
            $reservas = Reserva::where('id_servicio', $id_servicio)
                ->where('tipo_servicio', 'evento')
                ->where('estado', '!=', 'cancelada')
                ->get();

            $lugaresOcupados = [];

            foreach ($reservas as $reserva) {
                // Decodificar el JSON de detalle_1 que contiene los lugares
                $lugares = json_decode($reserva->detalle_1, true);

                if (is_array($lugares)) {
                    $lugaresOcupados = array_merge($lugaresOcupados, $lugares);
                }
            }

            Log::info('Lugares ocupados encontrados:', ['lugares' => $lugaresOcupados]);

            return response()->json($lugaresOcupados);
        } catch (\Exception $e) {
            Log::error('Error al obtener lugares ocupados:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Error al obtener los lugares ocupados',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

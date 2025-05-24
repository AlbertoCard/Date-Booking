<?php

namespace App\Http\Controllers;

use App\Models\Reserva;

use App\Models\Servicio;
use App\Models\Disponibilidad;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

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
            ->where(function ($query) use ($request) {
                $fecha = Carbon::parse($request->fecha);
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

        // Extraer hora de la fecha solicitada
        $horaReserva = Carbon::parse($request->fecha)->format('H:i:s');

        // Extraer hora inicio y fin de la disponibilidad
        $horaInicio = Carbon::parse($disponibilidad->hora_inicio)->format('H:i:s');
        $horaFin = Carbon::parse($disponibilidad->hora_fin)->format('H:i:s');

        // Validar que la hora de la reserva esté dentro del rango permitido
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

        // Verificar disponibilidad del servicio
        $disponibilidad = Disponibilidad::where('id_servicio', $request->id_servicio)
            ->where(function ($query) use ($request) {
                $fecha = Carbon::parse($request->fecha);
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
        
    }
}

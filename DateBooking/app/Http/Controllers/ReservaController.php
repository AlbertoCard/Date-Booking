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

            // Generar horas disponibles
            $horaInicio = Carbon::parse($disponibilidad->hora_inicio);
            $horaFin = Carbon::parse($disponibilidad->hora_fin);
            $intervalo = Carbon::parse($disponibilidad->intervalo)->diffInMinutes(Carbon::today());

            $horasDisponibles = [];
            $horaActual = $horaInicio->copy();

            while ($horaActual <= $horaFin) {
                // Verificar si ya hay una reserva para esta hora
                $reservaExistente = Reserva::where('id_servicio', $id_servicio)
                    ->where('fecha', $fecha->format('Y-m-d') . ' ' . $horaActual->format('H:i:s'))
                    ->where('estado', '!=', 'cancelada')
                    ->first();

                if (!$reservaExistente) {
                    $horasDisponibles[] = $horaActual->format('H:i');
                }

                $horaActual->addMinutes($intervalo);
            }

            return response()->json($horasDisponibles);
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

    
}

<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Servicio;
use App\Models\Disponibilidad;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validar la solicitud
            $request->validate([
                'id_servicio' => 'required|exists:servicios,id_servicio',
                'fecha' => 'required|date|after_or_equal:today',
                'hora' => 'required',
                'detalles' => 'required|array'
            ]);

            // Verificar disponibilidad
            $disponibilidad = Disponibilidad::where('id_servicio', $request->id_servicio)
                ->where('fecha', $request->fecha)
                ->first();

            if (!$disponibilidad) {
                return response()->json([
                    'message' => 'No hay disponibilidad para la fecha seleccionada'
                ], 400);
            }

            // Crear el pago pendiente
            $servicio = Servicio::findOrFail($request->id_servicio);
            $pago = Pago::create([
                'id_usuario' => auth()->user()->uid,
                'monto' => $servicio->costo,
                'estado_pago' => 'pendiente',
                'metodo_pago' => 'pendiente'
            ]);

            // Crear la reserva
            $reserva = Reserva::create([
                'id_usuario' => auth()->user()->uid,
                'id_servicio' => $request->id_servicio,
                'id_pago' => $pago->id_pago,
                'estado' => 'pendiente',
                'fecha' => $request->fecha . ' ' . $request->hora,
                'tipo_servicio' => $servicio->categoria,
                'detalle_1' => json_encode($request->detalles),
                'detalle_2' => ''
            ]);

            DB::commit();

            return response()->json([
                'id_reserva' => $reserva->id_reserva,
                'message' => 'Reserva creada exitosamente'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear la reserva',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getDisponibilidad($id_servicio, Request $request)
    {
        try {
            $fecha = Carbon::parse($request->fecha);
            
            $disponibilidad = Disponibilidad::where('id_servicio', $id_servicio)
                ->where(function($query) use ($fecha) {
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
} 
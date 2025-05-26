<?php

namespace App\Http\Controllers;

use App\Models\Disponibilidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DisponibilidadController extends Controller
{
    private $diasValidos = [
        "lunes",
        "martes",
        "miercoles",
        "jueves",
        "viernes",
        "sabado",
        "domingo"
    ];

    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_servicio' => 'required|exists:servicios,id_servicio',
                'fecha' => 'required|date',
                'hora_inicio' => 'required',
                'hora_fin' => 'required',
                'intervalo' => 'required',
                'dias' => ['required', 'string', function ($attribute, $value, $fail) {
                    $dias = explode(',', $value);
                    foreach ($dias as $dia) {
                        if (!in_array(trim($dia), $this->diasValidos)) {
                            Log::warning("Día inválido recibido en store: $dia");
                            $fail('El día ' . $dia . ' no es válido.');
                        }
                    }
                }],
                'tipo' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación en store: ' . $e->getMessage(), ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error inesperado en store: ' . $e->getMessage());
            throw $e;
        }

        try {
            $disponibilidad = Disponibilidad::create([
                'id_servicio' => $request->id_servicio,
                'fecha' => $request->fecha,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'intervalo' => $request->intervalo,
                'dias' => $request->dias,
                'tipo' => $request->tipo,
                'activo' => 1
            ]);
        } catch (\Exception $e) {
            Log::error('Error al crear disponibilidad: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear la disponibilidad'], 500);
        }

        return response()->json($disponibilidad, 201);
    }

    public function toggleActivo($id_servicio)
    {
        try {
            $disponibilidades = Disponibilidad::where('id_servicio', $id_servicio)->get();
        } catch (\Exception $e) {
            Log::error("Error al buscar disponibilidades en toggleActivo: " . $e->getMessage());
            return response()->json(['message' => 'Error al buscar disponibilidades'], 500);
        }

        if ($disponibilidades->isEmpty()) {
            Log::info("No se encontraron disponibilidades para el servicio $id_servicio en toggleActivo");
            return response()->json(['message' => 'No se encontraron disponibilidades para este servicio'], 404);
        }

        $nuevoEstado = $disponibilidades->first()->activo == 1 ? 0 : 1;

        foreach ($disponibilidades as $disponibilidad) {
            try {
                $disponibilidad->activo = $nuevoEstado;
                $disponibilidad->save();
            } catch (\Exception $e) {
                Log::error("Error al actualizar estado en toggleActivo para disponibilidad {$disponibilidad->id}: " . $e->getMessage());
                return response()->json(['message' => 'Error al actualizar el estado'], 500);
            }
        }

        return response()->json([
            'message' => 'Estado actualizado correctamente',
            'activo' => $nuevoEstado
        ]);
    }

    public function destroy($id_servicio)
    {
        try {
            $disponibilidades = Disponibilidad::where('id_servicio', $id_servicio)->get();
        } catch (\Exception $e) {
            Log::error("Error al buscar disponibilidades en destroy: " . $e->getMessage());
            return response()->json(['message' => 'Error al buscar disponibilidades'], 500);
        }

        if ($disponibilidades->isEmpty()) {
            Log::info("No se encontraron disponibilidades para el servicio $id_servicio en destroy");
            return response()->json(['message' => 'No se encontraron disponibilidades para este servicio'], 404);
        }

        foreach ($disponibilidades as $disponibilidad) {
            try {
                $disponibilidad->delete();
            } catch (\Exception $e) {
                Log::error("Error al eliminar disponibilidad {$disponibilidad->id} en destroy: " . $e->getMessage());
                return response()->json(['message' => 'Error al eliminar disponibilidades'], 500);
            }
        }

        return response()->json([
            'message' => 'Disponibilidades eliminadas correctamente'
        ]);
    }


    public function getByServicio($id_servicio)
    {
        try {
            $disponibilidad = Disponibilidad::where('id_servicio', $id_servicio)
                ->where('activo', 1)
                ->first();

            if (!$disponibilidad) {
                Log::info("No hay disponibilidad activa para el servicio $id_servicio en getByServicio");
                return response()->json([
                    'message' => 'No hay disponibilidad configurada para este servicio'
                ], 404);
            }

            return response()->json($disponibilidad);
        } catch (\Exception $e) {
            Log::error("Error al obtener la disponibilidad en getByServicio: " . $e->getMessage());
            return response()->json([
                'message' => 'Error al obtener la disponibilidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

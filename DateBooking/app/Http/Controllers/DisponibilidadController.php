<?php

namespace App\Http\Controllers;

use App\Models\Disponibilidad;
use Illuminate\Http\Request;

class DisponibilidadController extends Controller
{
    private $diasValidos = [
        "lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo"
    ];

    public function store(Request $request)
    {
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
                        $fail('El día ' . $dia . ' no es válido.');
                    }
                }
            }],
            'tipo' => 'required|string',
        ]);

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

        return response()->json($disponibilidad, 201);
    }

    public function toggleActivo($id_servicio)
    {
        $disponibilidades = Disponibilidad::where('id_servicio', $id_servicio)->get();
        
        if ($disponibilidades->isEmpty()) {
            return response()->json(['message' => 'No se encontraron disponibilidades para este servicio'], 404);
        }

        $nuevoEstado = $disponibilidades->first()->activo == 1 ? 0 : 1;

        foreach ($disponibilidades as $disponibilidad) {
            $disponibilidad->activo = $nuevoEstado;
            $disponibilidad->save();
        }

        return response()->json([
            'message' => 'Estado actualizado correctamente',
            'activo' => $nuevoEstado
        ]);
    }

    public function destroy($id_servicio)
    {
        $disponibilidades = Disponibilidad::where('id_servicio', $id_servicio)->get();
        
        if ($disponibilidades->isEmpty()) {
            return response()->json(['message' => 'No se encontraron disponibilidades para este servicio'], 404);
        }

        foreach ($disponibilidades as $disponibilidad) {
            $disponibilidad->delete();
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
                return response()->json([
                    'message' => 'No hay disponibilidad configurada para este servicio'
                ], 404);
            }

            return response()->json($disponibilidad);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener la disponibilidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

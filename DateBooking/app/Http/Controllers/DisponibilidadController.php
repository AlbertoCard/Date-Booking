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
            'dias' => 'required|string|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
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
}

<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function getByServicio($id_servicio)
    {
        try {
            $medicos = Medico::where('id_servicio', $id_servicio)->get();
            return response()->json($medicos);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los médicos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
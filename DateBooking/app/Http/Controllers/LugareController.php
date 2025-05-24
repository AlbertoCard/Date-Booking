<?php

namespace App\Http\Controllers;

use App\Models\Lugare;
use Illuminate\Http\Request;

class LugareController extends Controller
{
    public function getByServicio($id_servicio)
    {
        try {
            $lugares = Lugare::where('id_servicio', $id_servicio)
                ->orderBy('sector')
                ->orderBy('fila')
                ->orderBy('numero')
                ->get();

            return response()->json($lugares);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener los lugares',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 
<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    // lista de servicios
    public function index()
    {
        return Servicio::all();
    }

    public function search($search)
    {
        return Servicio::where('nombre', 'like', '%' . $search . '%')
            ->get();
    }
}

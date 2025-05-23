<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Habitacione extends Model
{
    //
    use HasFactory;

    protected $table = 'habitaciones';

    protected $fillable = [
        'id_servicio',
        'tipo',
        'numero',
        'capacidad',
        'fecha_inicio',
        'fecha_fin',
    ];
}

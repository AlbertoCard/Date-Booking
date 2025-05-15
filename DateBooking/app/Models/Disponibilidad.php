<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disponibilidad extends Model
{
    //
    protected $table = 'disponibilidad';
    protected $primaryKey = 'id_disponibilidad';

    protected $fillable = [
        'id_servicio',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'estado',
        'intervalo',
        'dias',
        'tipo',
        'activo'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}

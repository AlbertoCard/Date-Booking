<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';
    protected $primaryKey = 'id_servicio';

    protected $fillable = [
        'id_establecimiento',
        'nombre',
        'descripcion',
        'costo',
        'categoria',
    ];

    // ⬇️ Oculta campos en respuestas JSON
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
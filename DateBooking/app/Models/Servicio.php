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
        'id_ciudad'
    ];

    // ⬇️ Oculta campos en respuestas JSON
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function disponibilidad()
    {
        return $this->hasMany(Disponibilidad::class, 'id_servicio', 'id_servicio');
    }
}
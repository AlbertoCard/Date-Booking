<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Servicio extends Model
{
  use HasFactory;
  
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

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'id_establecimiento', 'id_establecimiento');
    }

    public function ciudad()
    {
        return $this->belongsTo(Ciudade::class, 'id_ciudad', 'id_ciudad');
    }
}   

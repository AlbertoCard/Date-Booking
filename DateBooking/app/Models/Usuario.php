<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    // Desactivamos timestamps ya que no los usamos en la migración
    public $timestamps = false;

    // Especificamos la tabla
    protected $table = 'usuarios';

    // Especificamos la llave primaria
    protected $primaryKey = 'uid';
    
    // Especificamos que la llave primaria es un string
    protected $keyType = 'string';

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'uid',
        'nombre',
        'email',
        'telefono',
        'foto_url',
        'rol',
        'fecha_creacion',
        'activo'
    ];

    // Cast de atributos
    protected $casts = [
        'fecha_creacion' => 'datetime',
        'activo' => 'boolean'
    ];
}

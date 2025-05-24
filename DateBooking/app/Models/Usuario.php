<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Usuario extends Model
{
    //
    use HasFactory;
    // Desactivamos timestamps ya que no los usamos en la migración
    public $timestamps = false;

    // Especificamos la tabla
    protected $table = 'usuarios';

    // Especificamos la llave primaria
    protected $primaryKey = 'uid';
    
    // Especificamos que la llave primaria es un string
    protected $keyType = 'string';
    public $incrementing = false;

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

    // Relacion con reservas
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_usuario', 'uid');
    }
}

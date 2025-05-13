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
        'activo'
    ];

    // Cast de atributos
    protected $casts = [
        'activo' => 'integer',
        'rol' => 'string',
        'telefono' => 'string'
    ];

    // Valores por defecto
    protected $attributes = [
        'telefono' => '0000000000',
        'rol' => 'cliente',
        'activo' => 1
    ];

    // Relación muchos a muchos con establecimientos
    public function establecimientos()
    {
        return $this->belongsToMany(Establecimiento::class, 'estb_xusuario', 'id_usuario', 'id_establecimiento');
    }

    // Método para verificar si es un establecimiento
    public function esEstablecimiento()
    {
        return $this->rol === 'establecimiento';
    }

    // Método para verificar si es un cliente
    public function esCliente()
    {
        return $this->rol === 'cliente';
    }
}

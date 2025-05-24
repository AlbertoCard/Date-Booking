<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Model
{
    //
    use HasFactory;
    //, HasApiTokens;
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

    // Relacion con reservas
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_usuario', 'uid');
    }
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

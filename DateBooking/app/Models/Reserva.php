<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reserva extends Model
{
    use HasFactory;


    protected $table = 'reservas';
    protected $primaryKey = 'id_reserva';

    protected $fillable = [
        'id_usuario',
        'id_servicio',
        'id_pago',
        'estado',
        'fecha',
        'tipo_servicio',
        'detalle_1',
        'detalle_2'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'uid');
    }

    // Relación con Servicio
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }

    // Relación con Pago
    public function pago()
    {
        return $this->belongsTo(Pago::class, 'id_pago', 'id_pago');
    }
}

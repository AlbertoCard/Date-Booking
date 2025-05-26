<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pago extends Model
{
    use HasFactory;
    
    protected $table = 'pagos';
    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'id_usuario',
        'id_reserva',
        'stripe_payment_intent_id',
        'stripe_customer_id',
        'monto',
        'moneda',
        'metodo_pago',
        'estado_pago',
        'fecha_pago'
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

    // Relación con Reserva
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reserva', 'id_reserva');
    }
}

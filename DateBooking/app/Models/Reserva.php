<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Reserva extends Model
{
    //
    use HasFactory;


    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }
    
}

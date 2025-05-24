<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Medico extends Model
{
    //
    use HasFactory;

    protected $table = 'medicos';

    protected $primaryKey = 'id_medico';
    
    protected $fillable = [
        'id_servicio',
        'nombre',
        'especialidad',
    ];
}

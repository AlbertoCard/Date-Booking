<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudades';
    protected $primaryKey = 'id_ciudad';

    protected $fillable = [
        'nombre',
        'id_estado'
    ];
    
}

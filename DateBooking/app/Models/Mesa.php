<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mesa extends Model
{
    //
    use HasFactory;

    protected $table = 'mesas';

    protected $fillable = [
        'id_servicio',
        'personas',
    ];
}

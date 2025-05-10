<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $fillable = ['uid', 'nombre', 'email', 'telefono', 'foto_url', 'rol'];

    protected $table = 'usuarios';
    protected $primaryKey = 'uid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $casts = [
        'uid' => 'string',
    ];
    
    //
}

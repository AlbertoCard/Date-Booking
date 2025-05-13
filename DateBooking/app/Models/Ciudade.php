<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudade extends Model
{
    public $timestamps = false;
    
    protected $table = 'estb_xusuario';

    protected $fillable = [
        'id_usuario',
        'id_establecimiento'
    ];

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'uid');
    }

    // Relación con Establecimiento
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'id_establecimiento', 'id_establecimiento');
    }
    //
}

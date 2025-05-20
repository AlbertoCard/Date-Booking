<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Establecimiento extends Model
{
    public $timestamps = false;
    
    protected $table = 'establecimientos';
    
    protected $primaryKey = 'id_establecimiento';

    protected $fillable = [
        'nombre',
        'telefono',
        'direccion',
        'rfc',
        'estado',
        'stripe_account_id'
    ];

    // Relación muchos a muchos con usuarios
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'estb_xusuario', 'id_establecimiento', 'id_usuario');
    }
    //
    use HasFactory;
}

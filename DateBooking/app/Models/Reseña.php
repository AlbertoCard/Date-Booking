<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reseña extends Model
{
    use HasFactory;

    protected $table = 'reseñas';
    protected $primaryKey = 'id_reseña';
    public $timestamps = true;

    protected $fillable = [
        'id_usuario',
        'id_servicio',
        'calificacion',
        'descripcion',
        'fecha'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'uid');
    }
}
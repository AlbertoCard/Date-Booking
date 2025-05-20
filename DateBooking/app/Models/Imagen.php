<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Imagen extends Model
{
    use HasFactory;

    protected $table = 'imgenes';
    protected $primaryKey = 'id_imagen';

    protected $fillable = [
        'id_servicio',
        'url'
    ];

      // ⬇️ Oculta campos en respuestas JSON
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio', 'id_servicio');
    }
} 
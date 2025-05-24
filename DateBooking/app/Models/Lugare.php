<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lugare extends Model
{
    use HasFactory;

    protected $table = 'lugares';
    protected $primaryKey = 'id_lugar';

    protected $fillable = [
        'id_servicio',
        'fila',
        'numero',
        'sector'
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstabXusuario extends Model
{
    //
    use HasFactory;
    protected $table = 'estb_xusuario';
    protected $fillable = [
        'id_usuario',
        'id_establecimiento',
    ];
}

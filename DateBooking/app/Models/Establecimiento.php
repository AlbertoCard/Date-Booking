<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Establecimiento extends Model
{
    //
    use HasFactory;
    protected $table = 'establecimientos';
    protected $primaryKey = 'id_establecimiento';
}

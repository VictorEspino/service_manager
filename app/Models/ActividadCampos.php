<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadCampos extends Model
{
    use HasFactory;

    protected $fillable=['actividad_id','etiqueta','tipo_control','requerido','lista_id'];
}

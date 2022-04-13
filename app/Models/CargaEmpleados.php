<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaEmpleados extends Model
{
    protected $fillable=['numero_empleado',
                        'nombre',
                        'area',
                        'subarea',
                        'puesto',
                        'estatus',
                        'carga_id',
                        'user_id_carga'];
    use HasFactory;
}

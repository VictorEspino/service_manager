<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadTicket extends Model
{
    use HasFactory;
    
    protected $fillable=[
        'ticket_id',
        'secuencia',
        'nombre',
        'descripcion',
        'sla',
        'grupo_id',
        'tipo_asignacion'
    ];
}

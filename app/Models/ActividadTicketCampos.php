<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadTicketCampos extends Model
{
    use HasFactory;
    protected $fillable=[
        'actividad_ticket_id',
        'etiqueta',
        'tipo_control',
        'requerido',
        'lista_id',
        'referencia',
        'valor'
    ];

}

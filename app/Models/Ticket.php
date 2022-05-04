<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable=[
        'creador_id',
        'de_id',
        'area_id',
        'subarea_id',
        'topico_id',
        'asunto',
        'descripcion',
        'prioridad',
        'asignado_a', 
        'a_a0',
        'n_actividades',
        'n_minutos',
        'emite_autorizacion'
    ];

    public function solicitante()
    {
        return $this->belongsTo(User::class,'de_id');
    }
    public function topico()
    {
        return $this->belongsTo(Topico::class,'topico_id');
    }
    public function asesor()
    {
        return $this->belongsTo(User::class,'asignado_a');
    }
    public function actividades()
    {
        return $this->hasMany(ActividadTicket::class);
    }
    public function area_solicitante()
    {
        return $this->belongsTo(Area::class,'area_id');
    }
    public function subarea_solicitante()
    {
        return $this->belongsTo(SubArea::class,'subarea_id');
    }
}

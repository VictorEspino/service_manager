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
        'topico_id',
        'asunto',
        'descripcion',
        'prioridad',
        'asignado_a',
    ];

    public function solicitante()
    {
        return $this->belongsTo(User::class,'de_id');
    }
    public function topico()
    {
        return $this->belongsTo(Topico::class,'topico_id');
    }
}

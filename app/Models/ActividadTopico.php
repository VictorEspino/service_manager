<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadTopico extends Model
{
    use HasFactory;

    protected $fillable=['topico_id','sla','grupo_id','tipo_asignacion','nombre','descripcion','secuencia','user_id_automatico'];

    public function topico()
    {
        return $this->belongsTo(Topico::class);
    }   
}

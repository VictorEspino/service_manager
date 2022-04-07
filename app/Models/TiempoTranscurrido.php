<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiempoTranscurrido extends Model
{
    use HasFactory;
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
}

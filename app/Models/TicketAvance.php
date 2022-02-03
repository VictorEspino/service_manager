<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAvance extends Model
{
    use HasFactory;

    protected $fillable=[
        'ticket_id',
        'user_id',
        'nombre_usuario',
        'avance',
        'tipo_avance',
        'adjunto',
        'archivo_adjunto',
    ];
    public function campos()
    {
        return $this->hasMany(TicketAvancesCampo::class,'ticket_avance_id');
    }  
}

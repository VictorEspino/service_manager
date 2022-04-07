<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoComunicacionPost extends Model
{
    use HasFactory;
    protected $fillable=['grupo_id','user_id','nombre_usuario','post','adjunto','archivo_adjunto'];

    public function comentarios()
    {
        return $this->hasMany(GrupoComunicacionPostComentario::class,'post_id');
    }
}

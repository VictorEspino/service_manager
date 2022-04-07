<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoComunicacionPostComentario extends Model
{
    use HasFactory;
    protected $fillable=['post_id','user_id','nombre_usuario','comentario'];
}

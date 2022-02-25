<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoComunicacion extends Model
{
    use HasFactory;

    protected $fillable=['nombre','descripcion'];
}

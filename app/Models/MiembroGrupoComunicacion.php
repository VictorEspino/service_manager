<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiembroGrupoComunicacion extends Model
{
    use HasFactory;

    protected $fillable=['grupo_id','user_id','manager'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

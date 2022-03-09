<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitado extends Model
{
    use HasFactory;
    protected $fillable=['actividad_id','user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

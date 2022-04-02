<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitadoTicket extends Model
{
    use HasFactory;

    protected $fillable=['ticket_id','user_id','actividad_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
